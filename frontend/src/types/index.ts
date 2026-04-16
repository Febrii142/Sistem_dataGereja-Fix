export type Role = 'admin' | 'pendeta' | 'koordinator' | 'user';

export interface AuthUser {
  id: string;
  name: string;
  email: string;
  role: Role;
}

export interface Member {
  id: string;
  nama: string;
  alamat: string;
  kontak: string;
  status: 'aktif' | 'tidak_aktif';
  tanggalLahir: string;
  jenisKelamin: 'L' | 'P';
  pekerjaan?: string;
  createdAt: string;
}

export interface Attendance {
  id: string;
  memberId: string;
  date: string;
  serviceType: 'minggu' | 'pemuda' | 'doa';
  hadir: boolean;
  note?: string;
  member?: Member | null;
}
