# AGENTS.md — SINTESA

## 1. Status Dokumen
Dokumen ini adalah aturan kerja utama untuk AI coding agent (termasuk Antigravity) saat mengembangkan SINTESA. AGENTS.md menjadi guardrail implementasi; PRD menjadi sumber kebenaran kebutuhan produk.

Jika instruksi agent bertentangan dengan PRD, prioritaskan:
1. Keamanan dan integritas data
2. Business rule yang ditetapkan PRD
3. Arsitektur Laravel monolith
4. Fitur yang sudah berjalan
5. UI/UX dan polish

Jangan mengubah arsitektur, role, database, atau business logic yang sudah bekerja tanpa alasan teknis yang kuat dan tanpa memastikan regresi tidak terjadi.

---

## 2. Product Identity
Nama: SINTESA
Kepanjangan: Sistem Integrasi Niaga Pertanian Cerdas

Positioning:
Platform integrasi niaga hasil pertanian berbasis web yang menghubungkan Petani dengan Pengepul dan Konsumen melalui marketplace, permintaan komoditas, pencocokan kebutuhan, negosiasi harga, pre-order, dan transaksi digital.

Tagline:
"Menghubungkan Petani, Memperluas Akses Pasar."

Role resmi:
- Petani
- Pengepul
- Konsumen
- Admin

CATATAN: Jangan menggunakan role "Mitra Pembeli" sebagai role terpisah. UMKM tidak menjadi role khusus dalam versi final SINTESA.

---

## 3. Technology Guardrails
WAJIB:
- Backend: Laravel versi stabil yang kompatibel
- Bahasa: PHP
- Frontend: Laravel Blade
- CSS: Tailwind CSS
- Interaksi: JavaScript
- Database: MySQL
- ORM: Laravel Eloquent
- Local environment: Laragon
- Database management: phpMyAdmin atau HeidiSQL
- Map: Leaflet.js + OpenStreetMap
- Chart: Chart.js
- Icons: Lucide Icons

Arsitektur:
Laravel monolith.

JANGAN menggunakan React, Next.js, Express, Node.js sebagai framework frontend/backend terpisah, kecuali ada instruksi eksplisit baru yang menggantikan aturan ini.

---

## 4. Core Engineering Rules
1. Jangan membuat UI statis yang tidak terhubung ke backend.
2. Jangan membuat tombol palsu.
3. Setiap form harus memiliki validasi server-side dan penyimpanan database jika memang persistent.
4. Setiap data demo harus berasal dari Seeder dan tetap dapat diganti/ditambah melalui CRUD.
5. SINTESA Match wajib menghitung dari data MySQL nyata.
6. Jangan hard-code hasil matching.
7. Jangan menggunakan dummy number pada dashboard jika data database tersedia.
8. Semua CRUD harus memvalidasi ownership dan authorization.
9. Gunakan Form Request untuk validasi kompleks.
10. Gunakan Policy/Middleware untuk authorization.
11. Business logic kompleks ditempatkan pada Service class, bukan dijejalkan ke Controller.
12. Gunakan Eloquent relationship dan foreign key.
13. Hindari duplikasi data.
14. Jangan menghapus fitur yang sudah berfungsi hanya demi mempercepat phase.
15. Setiap perubahan schema wajib menggunakan migration.
16. Jangan menyimpan data sensitif di frontend.
17. Gunakan CSRF protection dan authentication middleware bawaan Laravel.
18. Semua alur penting harus dapat diuji end-to-end.

---

## 5. Role & Authorization Rules

### Petani
Boleh:
- mengelola profil sendiri
- mencatat hasil panen sendiri
- mengelola stok sendiri
- membuat dan mengelola produk sendiri
- membuka pre-order
- melihat permintaan komoditas yang relevan
- mengajukan/menanggapi penawaran
- menerima/menolak pesanan
- melihat transaksi miliknya
- melihat SINTESA Match sesuai konteks

Tidak boleh:
- mengubah data petani lain
- mengubah transaksi milik pihak lain
- mengakses dashboard admin

### Pengepul
Boleh:
- mencari produk
- membuat permintaan komoditas
- mengajukan penawaran harga
- melakukan pembelian
- melakukan pre-order
- menggunakan SINTESA Match
- melihat peta petani
- melihat transaksi sendiri

Tidak boleh:
- mengubah produk petani lain
- mengakses dashboard admin
- mengubah transaksi pihak lain

### Konsumen
Boleh:
- mencari produk
- membeli produk
- membuat permintaan komoditas
- mengajukan penawaran harga jika produk mengizinkan
- melakukan pre-order
- menggunakan SINTESA Match
- melihat peta petani
- melihat transaksi sendiri

Tidak boleh:
- mengubah data petani lain
- mengakses dashboard admin

### Admin
Admin adalah pengelola sistem.

Boleh:
- mengelola pengguna
- mengelola komoditas
- mengelola/moderasi produk
- mengelola data harga komoditas
- memonitor transaksi
- memonitor aktivitas
- melihat statistik sistem

Admin TIDAK boleh dipilih melalui registrasi publik.
Admin dibuat melalui seeder/mekanisme khusus.

---

## 6. Authentication
- Register
- Login
- Logout
- Session management
- Protected routes
- Role middleware
- Authorization/Policy
- Ownership validation

Role yang dapat dipilih saat registrasi:
- Petani
- Pengepul
- Konsumen

Admin dibuat melalui seeder atau mekanisme khusus.

---

## 7. Business Rules

### Harvest → Stock → Marketplace
Alur wajib:
Hasil Panen → Stok → Produk Marketplace

Data panen:
- commodity
- quantity
- unit
- harvest_date
- quality
- location
- notes

Stok:
- available_quantity
- ordered_quantity
- sold_quantity
- unit
- status

Status stok:
- Tersedia
- Stok Terbatas
- Habis

Rules:
- stok tidak boleh negatif
- order tidak boleh melebihi stok
- perubahan stok harus konsisten dengan status transaksi
- jangan mengurangi stok dua kali akibat refresh/retry request

### Marketplace
Produk minimal:
- nama produk
- komoditas
- foto
- petani
- lokasi
- harga
- stok
- kualitas
- tanggal panen
- status

Fitur:
- search
- filter komoditas
- filter lokasi
- filter harga
- filter stok
- sorting
- detail produk
- pembelian
- penawaran
- pre-order jika tersedia

### Permintaan Komoditas
Hanya Pengepul dan Konsumen.

Data:
- commodity
- required_quantity
- unit
- max_price
- location
- deadline
- description
- status

Status:
- Aktif
- Mendapat Penawaran
- Dipenuhi
- Ditutup
- Dibatalkan

Petani dapat melihat permintaan yang relevan dan memberi penawaran.

### Penawaran Harga
Pengepul/Konsumen dapat mengajukan:
- produk
- buyer
- quantity
- offered_price
- product_price
- notes
- status

Status:
- Menunggu
- Diterima
- Ditolak
- Counter Offer
- Selesai
- Dibatalkan

Jika disepakati, sistem membuat order menggunakan harga yang disepakati.

### Pre-order
Petani dapat membuka pre-order berdasarkan rencana panen.

Data:
- commodity
- estimated_production
- price
- estimated_harvest_date
- preorder_available_quantity
- description

Status:
- Dibuka
- Menunggu Panen
- Siap Diproses
- Diproses
- Selesai
- Dibatalkan

Rule:
Jumlah pre-order tidak boleh melebihi kapasitas yang tersedia.

### Transaction
Sumber transaksi:
- pembelian langsung
- penawaran yang disetujui
- permintaan komoditas
- pre-order

Status:
- Menunggu Konfirmasi
- Dikonfirmasi
- Diproses
- Selesai
- Dibatalkan

Alur:
Pembeli membuat pesanan
→ Petani menerima notifikasi
→ Petani mengonfirmasi
→ Pesanan diproses
→ Transaksi selesai

---

## 8. SINTESA Match — Feature Paling Penting
SINTESA Match adalah core innovation dan wajib nyata.

Input:
- komoditas
- jumlah kebutuhan
- harga maksimal
- lokasi

Data kandidat diambil dari database MySQL.

Tahapan:
1. filter komoditas
2. cek stok
3. evaluasi harga
4. hitung jarak
5. hitung weighted score
6. urutkan score descending
7. tampilkan rekomendasi

Bobot:
- Kesesuaian Komoditas: 35%
- Ketersediaan Stok: 25%
- Kesesuaian Harga: 20%
- Kedekatan Lokasi: 20%

Match Score:
`(commodity × 0.35) + (stock × 0.25) + (price × 0.20) + (distance × 0.20)`

Kategori:
- 85–100: Sangat Cocok
- 70–84: Cocok
- 50–69: Cukup Cocok
- 0–49: Kurang Cocok

Implementasikan pada Service class yang maintainable.
Jangan hard-code ranking atau score.

---

## 9. Map & Price
Map:
- Leaflet.js
- OpenStreetMap
- koordinat disimpan di database
- marker menampilkan petani, komoditas, produk, stok, harga
- filter lokasi dan komoditas
- gunakan koordinat database sebagai fallback

Harga komoditas:
- komoditas
- harga
- lokasi
- tanggal
- perubahan harga
- harga terbaru
- tertinggi
- terendah
- tren

Gunakan Chart.js.
Jangan menyebut data sebagai "real-time" jika sumber real-time belum tersedia.

---

## 10. Notifications
Notifikasi berasal dari aktivitas nyata:
- pesanan baru
- penawaran baru
- counter offer
- permintaan relevan
- perubahan status transaksi
- perubahan status pre-order

Jangan membuat notifikasi palsu hanya untuk memenuhi UI.

---

## 11. Database
Gunakan migration, foreign key, indexes yang relevan, dan Eloquent relationships.

Tabel inti:
- users
- farmer_profiles
- collector_profiles
- consumer_profiles
- commodities
- harvests
- stocks
- products
- product_images
- commodity_requests
- request_offers
- price_offers
- preorders
- preorder_items
- orders
- order_items
- transactions
- commodity_prices
- locations
- notifications
- reviews

Jangan membuat tabel baru jika data sudah dapat direlasikan secara tepat pada tabel yang ada.

---

## 12. Project Structure
Gunakan struktur Laravel yang modular:

app/
- Models/
- Http/Controllers/
- Http/Middleware/
- Http/Requests/
- Services/
- Policies/

database/
- migrations/
- seeders/
- factories/

resources/views/
- layouts/
- auth/
- farmer/
- collector/
- consumer/
- admin/

resources/css/
resources/js/

routes/
- web.php
- api.php

---

## 13. UI/UX
Konsep:
Modern Agriculture + Digital Ecosystem + Data-Driven Platform.

Prinsip:
- clean
- modern
- profesional
- mudah dipahami
- konsisten
- responsive
- data-driven

Gunakan:
- card layout
- analytics dashboard
- chart
- sidebar
- empty state
- loading state
- error state
- success feedback

Landing page:
- Hero
- Problem
- Solution
- Cara Kerja
- Fitur Utama
- Statistik Ekosistem
- CTA
- Footer

Responsive:
- desktop
- tablet
- mobile
- mobile sidebar berfungsi
- tabel tetap usable
- form nyaman
- button mudah disentuh

---

## 14. Development Phases
Phase 1 — Foundation & Authentication
Phase 2 — Core Marketplace
Phase 3 — Harvest & Stock
Phase 4 — Order & Transaction
Phase 5 — Commodity Request, Negotiation & Pre-order
Phase 6 — SINTESA Match
Phase 7 — Maps & Commodity Price Analytics
Phase 8 — Admin Panel, Testing & Final Polish

Jangan melompat phase tanpa alasan.
Setiap phase harus tetap mempertahankan fitur sebelumnya.

---

## 15. Mandatory Testing Setelah Setiap Phase
Periksa:
- routes
- migration
- database relationships
- CRUD
- validation
- authorization
- ownership
- frontend-backend integration
- error pages
- end-to-end flow
- responsive behavior jika relevan

Jangan lanjut jika fitur inti phase belum berfungsi.

---

## 16. Demo Data
Seeder wajib menyediakan data realistis:
- Jagung
- Cabai
- Tomat
- Kelapa
- Padi

Data demo hanya untuk demonstrasi.
User baru tetap harus dapat membuat data sendiri.
Data baru harus langsung masuk ke alur sistem.

---

## 17. Definition of Done
Fitur dianggap selesai jika:
- UI tersedia
- route tersedia
- backend berjalan
- database terintegrasi
- validation tersedia
- authorization tersedia
- loading/empty/error/success state tersedia jika relevan
- data tersimpan dan terbaca kembali
- business rule berjalan
- tidak ada dummy functionality
- tidak ada broken page
- dapat diuji end-to-end

Prioritaskan kualitas implementasi daripada jumlah fitur.

---

## 18. Agent Behavior
Sebelum coding:
1. baca PRD dan AGENTS.md
2. periksa struktur project existing
3. identifikasi fitur yang sudah ada
4. jangan menimpa implementasi yang bekerja
5. rencanakan perubahan schema dan dependency

Saat coding:
1. buat perubahan kecil dan modular
2. gunakan pola Laravel yang konsisten
3. validasi input
4. cek authorization
5. gunakan transaction database untuk operasi multi-step yang membutuhkan atomicity
6. tangani error dengan jelas
7. jangan hard-code business data

Setelah coding:
1. jalankan migration/test yang relevan
2. periksa route
3. periksa database
4. uji role
5. uji happy path
6. uji invalid input
7. uji edge case stok
8. periksa responsive UI
9. pastikan tidak ada regresi

Jika requirement ambigu:
- pilih solusi yang paling konsisten dengan PRD
- jangan menambah fitur besar tanpa kebutuhan
- dokumentasikan asumsi pada output kerja

---

## 19. Absolute Don'ts
Jangan:
- membuat prototype statis
- membuat tombol tanpa fungsi
- menggunakan data statis untuk SINTESA Match
- menggunakan dashboard dummy
- mengizinkan stok negatif
- mengizinkan order melebihi stok
- membiarkan user mengakses data milik role lain
- membuat Admin dapat diregistrasikan publik
- mengganti Laravel monolith dengan SPA framework
- menghapus fitur existing yang sudah bekerja
- memasukkan UMKM sebagai role baru
