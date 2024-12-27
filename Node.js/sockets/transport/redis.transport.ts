import { Redis } from 'ioredis';
import { Injectable, Scope } from '@nestjs/common';
import { WsException } from '@nestjs/websockets';
import { RedisService } from '@songkeys/nestjs-redis';
import { TemplatedApp } from 'uWebSockets.js';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';
import { TransportInterface } from '@module/sockets/transport/transport.interface';

@Injectable({ scope: Scope.TRANSIENT })
export class RedisTransport implements TransportInterface {
  private readonly subscriber: Redis;
  private readonly publisher: Redis;

  constructor(private readonly redisService: RedisService) {
    const client = redisService.getClient('default');

    this.subscriber = client.duplicate();
    this.publisher = client.duplicate();
  }

  public onMessage(server: TemplatedApp): void {
    if (1 < this.subscriber.listenerCount('message')) {
      throw new WsException('Only one message listener can be set.');
    }

    this.subscriber.on('message', (topicId: string, message: Buffer) =>
      server.publish(topicId, message, false),
    );
  }

  public publish(topicId: string, message: Buffer): Promise<number> {
    return this.publisher.publish(topicId, message);
  }

  public async subscribe(client: WebSocketDeliveryInterface): Promise<void> {
    await this.subscriber.subscribe(client.topicId);

    client.deliver = (message: Buffer) => this.publisher.publish(client.topicId, message);
  }

  public async unsubscribe(client: WebSocketDeliveryInterface): Promise<void> {
    await this.subscriber.unsubscribe(client.topicId);
    await this.subscriber.quit();
  }
}
