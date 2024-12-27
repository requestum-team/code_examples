export default class MessageBuffer {
  static fromObject(obj): Buffer {
    return Buffer.from(JSON.stringify(obj));
  }

  static from(data): Buffer {
    return Buffer.from(data);
  }

  static toObject(buffer): Object {
    try {
      return JSON.parse(this.to(buffer));
    } catch (e: any) {
      return {};
    }
  }

  static to(data): string {
    return Buffer.from(data).toString();
  }
}
