export interface TransportInterface {
  onMessage(server: any): any;
  subscribe(client: any): any;
  unsubscribe(client: any): any;
}
