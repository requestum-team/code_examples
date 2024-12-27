import { TemplatedApp } from 'uWebSockets.js';
import { OnGatewayConnection, OnGatewayDisconnect, OnGatewayInit } from '@nestjs/websockets';
import { RedisTransport } from '@module/sockets/transport/redis.transport';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';
import { ConnectionService } from '@module/sockets/service/connection.service';

export abstract class AbstractGateway
  implements OnGatewayConnection, OnGatewayDisconnect, OnGatewayInit
{
  protected constructor(
    protected readonly transport: RedisTransport,
    protected readonly connectionService: ConnectionService,
  ) {}

  public afterInit(server: TemplatedApp): void {
    this.transport.onMessage(server);
  }

  public async handleConnection(client: WebSocketDeliveryInterface, ...args: any[]): Promise<void> {
    await this.transport.subscribe(client);
    await this.connectionService.create(client);
  }

  public async handleDisconnect(client: WebSocketDeliveryInterface): Promise<void> {
    await this.connectionService.delete(client);
  }
}
