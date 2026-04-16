import { randomUUID } from 'node:crypto';
import bcrypt from 'bcryptjs';
import type { Attendance, Member, Role, User } from '../types.js';

const now = () => new Date().toISOString();

const adminPasswordHash = bcrypt.hashSync('Admin123!', 10);

export const users: User[] = [
  {
    id: randomUUID(),
    name: 'Administrator',
    email: 'admin@gereja.local',
    passwordHash: adminPasswordHash,
    role: 'admin',
    createdAt: now(),
  },
];

export const members: Member[] = [
  {
    id: randomUUID(),
    nama: 'Yohanes Setiawan',
    alamat: 'Jl. Kasih No. 1',
    kontak: '081234567890',
    status: 'aktif',
    tanggalLahir: '1990-05-10',
    jenisKelamin: 'L',
    pekerjaan: 'Wiraswasta',
    createdAt: now(),
  },
  {
    id: randomUUID(),
    nama: 'Maria Kristina',
    alamat: 'Jl. Damai No. 7',
    kontak: '081298765432',
    status: 'aktif',
    tanggalLahir: '1988-11-02',
    jenisKelamin: 'P',
    pekerjaan: 'Guru',
    createdAt: now(),
  },
];

export const attendance: Attendance[] = [
  {
    id: randomUUID(),
    memberId: members[0].id,
    date: now(),
    serviceType: 'minggu',
    hadir: true,
    note: 'Tepat waktu',
  },
  {
    id: randomUUID(),
    memberId: members[1].id,
    date: now(),
    serviceType: 'doa',
    hadir: false,
    note: 'Sakit',
  },
];

export const createId = () => randomUUID();

export const safeUser = (user: User) => {
  const { passwordHash, ...rest } = user;
  return rest;
};

export const allowedRoles: Role[] = ['admin', 'pendeta', 'koordinator', 'user'];
