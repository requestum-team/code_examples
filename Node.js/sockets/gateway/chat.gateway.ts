import {
  ConnectedSocket,
  MessageBody,
  SubscribeMessage,
  WebSocketGateway,
} from '@nestjs/websockets';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';
import { AbstractGateway } from '@module/sockets/gateway/abstract.gateway';
import { RedisTransport } from '@module/sockets/transport/redis.transport';
import { ConnectionService } from '@module/sockets/service/connection.service';
import { SocketsService } from '@module/sockets/service/sockets.service';
import { MessageService } from '@module/message/service/message.service';
import { MemberService } from '@module/member/service/member.service';
import { Message } from '@module/message/entity/message';
import { JwtAuthGuard } from '@module/security/guard/ws/jwt.auth.guard';
import { AcGuard } from '@module/security/guard/ws/ac.guard';
import { MessageOwnGuard } from '@module/security/guard/ws/message.own.guard';
import {
  ClassSerializerInterceptor,
  UseFilters,
  UseInterceptors,
  UsePipes,
  ValidationPipe,
} from '@nestjs/common';
import { CreateMessageDto } from '@module/message/dto/socket/create.message.dto';
import { UpdateMessageDto } from '@module/message/dto/socket/update.message.dto';
import { DeleteMessageDto } from '@module/message/dto/socket/delete.message.dto';
import { SeenMessageDto } from '@module/message/dto/socket/seen.message.dto';
import { PresenceMessageDto } from '@module/message/dto/socket/presence.message.dto';
import {
  PersistentMessageType,
  TransparentMessageTypes,
} from '@module/message/entity/message.type';
import { AppWsExceptionFilter } from '@module/sockets/exception/ws.exception.filter';
import { CreateMessageInterceptor } from '@module/sockets/interceptor/create.message.interceptor';

@UsePipes(new ValidationPipe())
@UseFilters(new AppWsExceptionFilter())
@UseInterceptors(ClassSerializerInterceptor)
@WebSocketGateway({
  path: '/api/messaging/:topic/:token',
})
export class ChatGateway extends AbstractGateway {
  constructor(
    protected readonly transport: RedisTransport,
    protected readonly connectionService: ConnectionService,
    private readonly socketsService: SocketsService,
    private readonly messageService: MessageService,
    private readonly memberService: MemberService,
  ) {
    super(transport, connectionService);
  }

  @JwtAuthGuard()
  @AcGuard()
  @MessageOwnGuard()
  public async handleConnection(client: WebSocketDeliveryInterface, ...args): Promise<void> {
    await super.handleConnection(client, ...args);
    await this.connectionService.online(client);
  }

  public async handleDisconnect(client: WebSocketDeliveryInterface): Promise<void> {
    await super.handleDisconnect(client);
    await this.connectionService.offline(client);
  }

  @SubscribeMessage('default')
  public handleDefault(@MessageBody() body: CreateMessageDto): void {}

  @SubscribeMessage(PersistentMessageType.TEXT)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleText(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.LINK)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleLink(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.MEDIA)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleMedia(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.REPLY)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleReply(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.FORWARD)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleForward(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.MEMBER_LEFT)
  @UseInterceptors(CreateMessageInterceptor, ClassSerializerInterceptor)
  public handleLeave(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: CreateMessageDto,
  ): Promise<Message> {
    return this.messageService.createViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.UPDATE)
  public handleUpdate(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: UpdateMessageDto,
  ): Promise<Message> {
    return this.messageService.updateViaClient(client, body);
  }

  @SubscribeMessage(PersistentMessageType.DELETE)
  public handleDelete(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: DeleteMessageDto,
  ): Promise<Message> {
    return this.messageService.deleteViaClient(client, body);
  }

  @SubscribeMessage(TransparentMessageTypes.SEEN)
  public async handleSeen(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: SeenMessageDto,
  ): Promise<void> {
    await this.memberService.updateViaClient(client, { lastRead: body.body });
  }

  @SubscribeMessage(TransparentMessageTypes.PRESENCE)
  public handlePresence(
    @ConnectedSocket() client: WebSocketDeliveryInterface,
    @MessageBody() body: PresenceMessageDto,
  ): Message {
    return this.socketsService.presence(client);
  }
}
