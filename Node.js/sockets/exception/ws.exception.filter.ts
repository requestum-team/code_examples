import { ArgumentsHost, Catch, HttpException, WsExceptionFilter } from '@nestjs/common';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';
import MessageBuffer from '@module/sockets/utils/message.buffer';

@Catch(HttpException)
export class AppWsExceptionFilter implements WsExceptionFilter {
  public catch(exception: HttpException, host: ArgumentsHost): any {
    const client: WebSocketDeliveryInterface = host.switchToWs().getClient();
    const { message } = exception.getResponse() as any;

    client.send(MessageBuffer.fromObject({ type: 'validation_error', payload: message }));
  }
}
