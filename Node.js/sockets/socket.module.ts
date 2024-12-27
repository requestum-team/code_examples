import { Module } from '@nestjs/common';
import { ChatGateway } from '@module/sockets/gateway/chat.gateway';
import { NotificationGateway } from '@module/sockets/gateway/notification.gateway';
import { RedisTransport } from '@module/sockets/transport/redis.transport';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Instance } from '@module/sockets/entity/instance';
import { Connection } from '@module/sockets/entity/connection';
import { ConnectionService } from '@module/sockets/service/connection.service';
import { InstanceService } from '@module/sockets/service/instance.service';
import { SocketsService } from '@module/sockets/service/sockets.service';
import { SecurityModule } from '@module/security/security.module';
import { MessageModule } from '@module/message/message.module';
import { MemberModule } from '@module/member/member.module';

@Module({
  exports: [SocketsService, RedisTransport, SocketModule, ConnectionService, InstanceService],
  providers: [
    ChatGateway,
    NotificationGateway,
    RedisTransport,
    ConnectionService,
    SocketsService,
    InstanceService,
  ],
  imports: [
    SecurityModule,
    MessageModule,
    MemberModule,
    TypeOrmModule.forFeature([Instance, Connection]),
  ],
})
export class SocketModule {}
