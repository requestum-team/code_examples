import { WebSocketGateway } from '@nestjs/websockets';
import { AbstractGateway } from '@module/sockets/gateway/abstract.gateway';
import { RedisTransport } from '@module/sockets/transport/redis.transport';
import { JwtAuthGuard } from '@module/security/guard/ws/jwt.auth.guard';
import { AcGuard } from '@module/security/guard/ws/ac.guard';
import { NotificationOwnGuard } from '@module/security/guard/ws/notification.own.guard';
import { ConnectionService } from '@module/sockets/service/connection.service';
import { ClassSerializerInterceptor, UseInterceptors } from '@nestjs/common';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';

@UseInterceptors(ClassSerializerInterceptor)
@WebSocketGateway({
  path: '/api/notification/:topic/:token',
})
export class NotificationGateway extends AbstractGateway {
  constructor(
    protected readonly transport: RedisTransport,
    protected readonly connectionService: ConnectionService,
  ) {
    super(transport, connectionService);
  }

  @JwtAuthGuard()
  @AcGuard()
  @NotificationOwnGuard()
  async handleConnection(client: WebSocketDeliveryInterface, ...args): Promise<void> {
    return super.handleConnection(client, ...args);
  }
}
