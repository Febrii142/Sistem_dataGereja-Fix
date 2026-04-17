# Sistem Informasi Manajemen & Pendataan Jemaat Gereja (Laravel + Blade)

Implementasi awal website manajemen dan pendataan jemaat gereja berbasis **Laravel 13 + Blade** dengan struktur siap pengembangan lanjut dan deployment.

## Fitur Utama (Implementasi Awal)

1. **Dashboard Admin**
   - Ringkasan statistik jemaat (total, aktif, tidak aktif)
   - Grafik pertumbuhan jemaat bulanan
   - Grafik rata-rata kehadiran
   - Quick actions ke menu utama

2. **Manajemen Data Jemaat**
   - CRUD data jemaat (nama, alamat, kontak, status, tanggal lahir, jenis kelamin, pekerjaan)
   - Pencarian dan filter status
   - Export Excel (`maatwebsite/excel`)
   - Export PDF (`barryvdh/laravel-dompdf`)
   - Import file Excel/CSV

3. **Manajemen Kehadiran**
   - Input kehadiran ibadah
   - Laporan kehadiran per jemaat
   - Statistik hadir/tidak hadir

4. **User Management & Auth**
   - Login/Register berbasis session Laravel
   - RBAC berbasis `roles`, `permissions`, dan `role_permissions`
   - Role bawaan: `Admin`, `Pendeta`, `Staff`, `Member`
   - Manajemen akun pengguna + assignment role berbasis permission

5. **Laporan & Analytics**
   - Laporan demografis jemaat
   - Laporan pertumbuhan jemaat
   - Laporan kehadiran
   - Export laporan PDF

## Struktur Folder Utama

```txt
app/
  Http/Controllers/
  Http/Middleware/
  Models/
  Exports/
  Imports/
database/
  migrations/
  seeders/
resources/views/
  layouts/
  auth/
  dashboard/
  members/
  attendances/
  users/
  reports/
routes/web.php
```

## Setup Lokal

1. Install dependency:
   ```bash
   composer install
   npm install
   ```
2. Copy env:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Atur koneksi MySQL di `.env`
4. Migrasi + seed:
   ```bash
   php artisan migrate --seed
   ```
5. Jalankan aplikasi:
   ```bash
   composer run dev
   ```

Akses: `http://127.0.0.1:8000`

## Akun Default Seeder

- Email: `admin@gereja.local`
- Password: `Admin123!`
- Role: `Admin`
