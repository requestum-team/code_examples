import { Entity, PrimaryColumn } from 'typeorm';
import { Timestampable } from '@baseEntity/timestampable';

@Entity({ name: 'instances' })
export class Instance extends Timestampable {
  @PrimaryColumn({ type: 'uuid' })
  public id: string;
}
