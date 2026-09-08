# Rencana Implementasi Phase 1 — Foundation & Authentication SINTESA

Dokumen ini menjelaskan rencana teknis untuk mengimplementasikan **Phase 1** dari SINTESA (*Sistem Integrasi Niaga Pertanian Cerdas*) sesuai panduan [AGENTS.md](file:///d:/laragon/www/Sintesa/AGENTS.md) dan [PRD_SINTESA.md](file:///d:/laragon/www/Sintesa/PRD_SINTESA.md).

---

## 1. Ringkasan & Ruang Lingkup Phase 1
Phase 1 berfokus pada fondasi arsitektur Laravel monolith, skema database inti, sistem autentikasi multi-role (Petani, Pengepul, Konsumen, Admin), middleware autorisasi, starter dashboard per role, dan data seeder komoditas realistis (Jagung, Cabai, Tomat, Kelapa, Padi).

### User Review Required
> [!IMPORTANT]
> **Koneksi MySQL Laragon:**
> Pastikan service MySQL di Laragon sudah aktif ("Start All" pada Laragon) agar proses migrasi dan seeding database `sintesa` dapat berjalan lancar. Database `sintesa` akan dibuat secara otomatis jika belum ada.
>
> **Aturan Keamanan Role Publik:**
> Form registrasi publik hanya memperbolehkan pemilihan role: **Petani**, **Pengepul**, dan **Konsumen**. Akun **Admin** tidak pernah bisa didaftarkan via form publik, melainkan dibuat melalui seeder `admin@sintesa.id`.

---

## 2. Rincian Teknis & Arsitektur

### A. Inisialisasi Laravel Monolith
- Memasang Laravel terbaru yang kompatibel dengan PHP 8.2 di direktori `d:/laragon/www/Sintesa` tanpa menghapus dokumen acuan `AGENTS.md` dan `PRD_SINTESA.md`.
- Konfigurasi `.env`:
  - `APP_NAME="SINTESA"`
  - `DB_CONNECTION=mysql`
  - `DB_DATABASE=sintesa`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=` (default Laragon)
- Integrasi Tailwind CSS dan Lucide Icons via CDN/Vite untuk UI modern berbasis Blade.

### B. Skema Database & Migrasi
1. **`users`**:
   - `id`, `name`, `email`, `password`, `role` (enum: `'petani'`, `'pengepul'`, `'konsumen'`, `'admin'`), `phone`, `avatar`, `is_active`, timestamps.
2. **`farmer_profiles`**:
   - `id`, `user_id` (foreign key -> users.id, cascade), `farm_name`, `farm_area_hectares`, `primary_commodity`, `address`, `latitude`, `longitude`, timestamps.
3. **`collector_profiles`**:
   - `id`, `user_id` (foreign key -> users.id, cascade), `business_name`, `business_type`, `address`, `latitude`, `longitude`, timestamps.
4. **`consumer_profiles`**:
   - `id`, `user_id` (foreign key -> users.id, cascade), `address`, `latitude`, `longitude`, timestamps.
5. **`commodities`**:
   - `id`, `name` (Jagung, Cabai, Tomat, Kelapa, Padi, dll), `slug`, `category`, `unit` (kg, ton, butir), `description`, `icon`, `is_active`, timestamps.

### C. Autentikasi & Autorisasi Multi-Role
- **Registrasi (`/register`)**:
  - Validasi form server-side (Form Request).
  - Pilihan role publik: `petani`, `pengepul`, `konsumen`.
  - Transaksi database atomik: membuat record `users` dan otomatis inisialisasi record profile (`farmer_profiles`, `collector_profiles`, atau `consumer_profiles`).
- **Login (`/login`)**:
  - Validasi credentials dengan CSRF protection bawaan Laravel.
  - Redirect otomatis berbasis role:
    - Petani $\rightarrow$ `/farmer/dashboard`
    - Pengepul $\rightarrow$ `/collector/dashboard`
    - Konsumen $\rightarrow$ `/consumer/dashboard`
    - Admin $\rightarrow$ `/admin/dashboard`
- **Logout (`/logout`)**: Invalidate session dan regenerate token.
- **Middleware Autorisasi (`RoleMiddleware`)**:
  - Memastikan user hanya dapat mengakses rute sesuai hak role-nya.
  - Mencegah akses silang (misal Petani mengakses `/admin/dashboard` atau Pengepul mengakses `/farmer/harvests` $\rightarrow$ 403 Forbidden).

### D. Layouts & Starter Views
- **Landing Page (`/`)**:
  - Tampilan modern bertema *Modern Agriculture + Digital Ecosystem*:
  - Hero section, problem-solution, diagram alur 2-way marketplace, statistik ekosistem, dan navigasi CTA.
- **Master Layout (`layouts/app.blade.php`) & Dashboard Layout (`layouts/dashboard.blade.php`)**:
  - Sidebar responsif (bisa dibuka-tutup di mobile).
  - Ikon modern menggunakan Lucide Icons.
  - Top navigation bar dengan profil user, role badge, dan tombol logout.
  - Flash message toast (success, error, warning).
- **Dashboard Awal per Role**:
  - Petani: ringkasan hasil panen, stok, produk, pesanan, dan menu navigasi panen/stok/marketplace.
  - Pengepul: ringkasan permintaan komoditas, pencarian produk, pesanan, dan SINTESA Match preview.
  - Konsumen: ringkasan belanja, pre-order, dan katalog komoditas.
  - Admin: statistik sistem pengguna dan komoditas.

### E. Seeder Realistis
- **`DatabaseSeeder`**:
  - `CommoditySeeder`: Data master komoditas wajib (Jagung, Cabai, Tomat, Kelapa, Padi) lengkap dengan satuan dan deskripsi.
  - `UserSeeder`: Akun demo untuk pengujian:
    - Admin: `admin@sintesa.id` / `password`
    - Petani: `petani@sintesa.id` / `password` (Pak Supardi - Tani Makmur)
    - Pengepul: `pengepul@sintesa.id` / `password` (CV Hasil Bumi Nusantara)
    - Konsumen: `konsumen@sintesa.id` / `password` (Ibu Sari - Rumah Tangga)

---

## 3. Rencana Verifikasi & Pengujian
1. **Verifikasi Database & Migrasi**:
   - `php artisan migrate:status` memastikan seluruh migrasi sukses.
   - `php artisan db:seed` memastikan data komoditas dan 4 user demo terisi.
2. **Verifikasi Autentikasi**:
   - Uji registrasi role Petani $\rightarrow$ cek profil petani dibuat di DB $\rightarrow$ diarahkan ke `/farmer/dashboard`.
   - Uji registrasi role Pengepul $\rightarrow$ cek profil pengepul dibuat di DB $\rightarrow$ diarahkan ke `/collector/dashboard`.
   - Uji registrasi role Konsumen $\rightarrow$ cek profil konsumen dibuat di DB $\rightarrow$ diarahkan ke `/consumer/dashboard`.
   - Uji upaya injeksi role `admin` pada form register $\rightarrow$ ditolak oleh validasi server-side.
3. **Verifikasi Autorisasi (Role Boundary)**:
   - Login sebagai Petani, coba akses `/admin/dashboard` $\rightarrow$ mendapatkan respon 403 / Redirect aman.
   - Login sebagai Konsumen, coba akses `/farmer/dashboard` $\rightarrow$ mendapatkan respon 403 / Redirect aman.
   - Login sebagai Admin (`admin@sintesa.id`) $\rightarrow$ sukses masuk `/admin/dashboard`.
4. **Verifikasi Tampilan Responsif**:
   - Cek tampilan landing page dan dashboard pada resolusi desktop dan mobile.
