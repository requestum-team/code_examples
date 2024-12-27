import { Entity, JoinColumn, ManyToOne, PrimaryColumn } from 'typeorm';
import { Timestampable } from '@baseEntity/timestampable';
import { Instance } from '@module/sockets/entity/instance';
import { Member } from '@module/member/entity/member';
import { User } from '@module/user/entity/user';

@Entity({ name: 'connections' })
export class Connection extends Timestampable {
  @PrimaryColumn({ type: 'uuid' })
  public id: string;

  @ManyToOne(() => Instance, {
    onDelete: 'CASCADE',
    nullable: false,
  })
  public instance: Instance;

  @ManyToOne(() => Member, {
    onDelete: 'SET NULL',
  })
  @JoinColumn()
  public member: Member;

  @ManyToOne(() => User, {
    onDelete: 'CASCADE',
    nullable: false,
  })
  @JoinColumn()
  public user: User;

  public get memberId(): string {
    return this.member.id;
  }
}
