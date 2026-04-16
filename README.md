# Sistem Informasi Manajemen & Pendataan Jemaat Gereja

Starter project full-stack untuk sistem manajemen jemaat gereja dengan arsitektur siap production dan mudah dikembangkan.

## Tech Stack
- **Frontend**: React + TypeScript + Vite + Tailwind CSS
- **Backend**: Node.js + Express + TypeScript
- **Auth**: JWT + Role-based access control
- **Data**: In-memory seed (siap dihubungkan ke MongoDB/PostgreSQL sesuai kebutuhan)

## Fitur yang Sudah Diimplementasikan (Initial Solid Implementation)
### 1) Dashboard Admin
- Statistik jemaat (total, aktif, tidak aktif, rasio kehadiran)
- Grafik pertumbuhan jemaat bulanan (Recharts)
- Quick action cards

### 2) Manajemen Data Jemaat
- Form input data jemaat (nama, alamat, kontak, status, tanggal lahir, jenis kelamin, pekerjaan)
- Daftar jemaat + pencarian
- Hapus data jemaat
- Export data ke **Excel** dan **PDF**
- Import data dari **CSV**

### 3) Manajemen Kehadiran
- Pencatatan kehadiran ibadah
- Daftar kehadiran per jemaat
- Statistik endpoint kehadiran per jemaat

### 4) User Management
- Login/Register backend
- RBAC role: `admin`, `pendeta`, `koordinator`, `user`
- Update role user (halaman user management)

### 5) Laporan & Analytics
- Laporan demografis jemaat
- Laporan pertumbuhan jemaat
- Laporan kehadiran
- Export laporan PDF

## Struktur Folder

```txt
.
├── backend/
│   ├── src/
│   │   ├── config/
│   │   ├── middleware/
│   │   ├── routes/
│   │   ├── store/
│   │   ├── tests/
│   │   ├── app.ts
│   │   └── server.ts
│   ├── package.json
│   └── tsconfig.json
├── frontend/
│   ├── src/
│   │   ├── components/
│   │   ├── lib/
│   │   ├── pages/
│   │   ├── tests/
│   │   └── types/
│   ├── package.json
│   └── vite.config.ts
├── .env.example
└── package.json
```

## Menjalankan Proyek

### 1. Install dependencies
```bash
npm install
npm --prefix backend install
npm --prefix frontend install
```

### 2. Setup environment
```bash
cp .env.example .env
```

### 3. Jalankan backend + frontend bersamaan
```bash
npm run dev
```

- Frontend: `http://localhost:5173`
- Backend: `http://localhost:4000`

## Akun Default
- Email: `admin@gereja.local`
- Password: `Admin123!`

## Scripts
- Root: `npm run dev`, `npm run build`, `npm run test`
- Backend: `npm run dev`, `npm run build`, `npm run test`
- Frontend: `npm run dev`, `npm run build`, `npm run test`, `npm run lint`

## Catatan Pengembangan Lanjutan
- Ganti in-memory store dengan MongoDB/PostgreSQL persistence layer.
- Tambahkan refresh token, audit log, dan hardening security untuk production.
- Tambahkan pagination server-side dan filtering lanjutan.
