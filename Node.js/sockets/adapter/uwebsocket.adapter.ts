import { WsMessageHandler } from '@nestjs/common';
import { EMPTY, filter, map, mergeMap, Observable } from 'rxjs';
import { GatewayMetadata } from '@nestjs/websockets/interfaces/gateway-metadata.interface';
import { DefaultBehavior } from '@module/sockets/behavior/default.behavior';
import { WebSocketDeliveryInterface } from '@module/sockets/adapter/websocket.delivery.interface';
import { AbstractWsAdapter } from '@nestjs/websockets';
import { Server } from '@module/http/server';
import { isNil } from '@nestjs/common/utils/shared.utils';
import MessageBuffer from '@module/sockets/utils/message.buffer';

export class UWebsocketAdapter<
  TServer extends Server = Server,
  TClient extends WebSocketDeliveryInterface = WebSocketDeliveryInterface,
  TOptions extends GatewayMetadata = GatewayMetadata,
> extends AbstractWsAdapter<TServer, TClient, TOptions> {
  private options: TOptions;

  public bindClientConnect(server: TServer, callback: Function): void {
    server.ws(this.options.path, new DefaultBehavior(callback));
  }

  public bindClientDisconnect(client: TClient, callback: Function): void {
    client.disconnect = callback;
  }

  public bindMessageHandlers(
    client: TClient,
    handlers: WsMessageHandler[],
    transform: (data: any) => Observable<any>,
  ): void {
    client.subject
      .pipe(
        map((data: any) => MessageBuffer.toObject(data.message)),
        mergeMap((message: any) => this.bindMessageHandler(message, handlers, transform)),
        filter((message: any) => !isNil(message)),
        map((message: any) => MessageBuffer.fromObject(message)),
      )
      .subscribe((message) => client.deliver(message));
  }

  public bindMessageHandler(
    message: any,
    handlers: WsMessageHandler[],
    transform: (data: any) => Observable<any>,
  ): Observable<any> {
    const messageHandler = handlers.find(
      (handler: WsMessageHandler) => handler.message === (message.type || 'default'),
    );

    if (!messageHandler) {
      return EMPTY;
    }

    return transform(messageHandler.callback(message));
  }

  public async close(server: TServer): Promise<void> {
    return server.close();
  }

  public create(port: number, options: TOptions): TServer {
    this.options = options;

    return this.httpServer;
  }
}
