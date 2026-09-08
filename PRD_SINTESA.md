# PRD — SINTESA
## Product Requirements Document

### 1. Ringkasan Produk
SINTESA (Sistem Integrasi Niaga Pertanian Cerdas) adalah platform web untuk mengintegrasikan perdagangan hasil pertanian dengan mempertemukan Petani, Pengepul, dan Konsumen dalam satu ekosistem digital.

SINTESA tidak hanya berfungsi sebagai marketplace. Sistem mencakup pencatatan hasil panen, manajemen stok, marketplace, permintaan komoditas, SINTESA Match, negosiasi harga, pre-order, pemesanan, transaksi, informasi harga, dan peta lokasi petani.

### 2. Masalah
Petani membutuhkan akses pasar yang lebih luas dan pencatatan hasil pertanian yang terstruktur. Pengepul membutuhkan cara yang lebih cepat untuk menemukan komoditas dan petani yang sesuai kebutuhan. Konsumen membutuhkan informasi produk, harga, lokasi, dan ketersediaan yang lebih jelas.

### 3. Tujuan
1. Mempermudah petani memasarkan hasil pertanian.
2. Membantu pengepul menemukan petani dan komoditas sesuai kebutuhan.
3. Membantu konsumen menemukan produk pertanian.
4. Meningkatkan transparansi informasi harga.
5. Mempermudah pencatatan hasil panen dan stok.
6. Memungkinkan pembeli membuat permintaan komoditas.
7. Memungkinkan pembelian melalui pre-order.
8. Memfasilitasi negosiasi harga.
9. Memberikan rekomendasi menggunakan SINTESA Match berbasis Weighted Scoring.

### 4. Target Pengguna
#### Petani
Produsen hasil pertanian yang menjual produk.

#### Pengepul
Pembeli hasil pertanian dalam jumlah besar untuk distribusi/perdagangan.

#### Konsumen
Pembeli hasil pertanian untuk kebutuhan konsumsi atau pembelian individu.

#### Admin
Pengelola, verifikator, dan pengawas sistem.

### 5. Konsep Marketplace
SINTESA menggunakan konsep Two-Way Agricultural Marketplace:
- Petani dapat menawarkan hasil pertanian.
- Pengepul dan Konsumen dapat mencari produk.
- Pengepul dan Konsumen juga dapat membuat permintaan komoditas.
- Petani dapat merespons permintaan tersebut.

Alur umum:
Petani → Hasil Panen → Stok → Marketplace
Pembeli → Search/Request → SINTESA Match → Penawaran/Order → Transaksi

### 6. Modul Fungsional

#### 6.1 Authentication & Role
- register
- login
- logout
- role-based middleware
- session
- protected routes
- ownership validation

Role registrasi publik:
- Petani
- Pengepul
- Konsumen

Admin dibuat melalui seeder/mekanisme khusus.

#### 6.2 Dashboard Petani
Menampilkan:
- total hasil panen
- total stok
- produk aktif
- pesanan masuk
- penawaran aktif
- total transaksi
- grafik produksi
- grafik penjualan
- komoditas utama

#### 6.3 Dashboard Pengepul
Menampilkan:
- permintaan aktif
- pesanan aktif
- total pembelian
- rekomendasi petani
- transaksi terbaru

#### 6.4 Dashboard Konsumen
Menampilkan:
- pesanan aktif
- pre-order aktif
- permintaan komoditas
- rekomendasi petani
- transaksi terbaru

#### 6.5 Dashboard Admin
Menampilkan:
- total pengguna
- total petani
- total pengepul
- total konsumen
- total produk
- total transaksi
- komoditas populer
- pertumbuhan pengguna
- grafik transaksi

#### 6.6 Manajemen Hasil Panen
Khusus Petani.

Field:
- komoditas
- jumlah
- satuan
- tanggal panen
- kualitas
- lokasi
- catatan

Wajib terintegrasi dengan stok.

#### 6.7 Manajemen Stok
Field:
- komoditas
- tersedia
- dipesan
- terjual
- satuan
- status

Status:
- Tersedia
- Stok Terbatas
- Habis

Rules:
- tidak boleh negatif
- tidak boleh order melebihi stok
- perubahan stok harus konsisten dengan transaksi

#### 6.8 Marketplace
Fitur:
- daftar produk
- search
- filter komoditas
- filter lokasi
- filter harga
- filter stok
- sorting
- detail produk
- beli langsung
- penawaran
- pre-order jika tersedia

Produk minimal:
- nama
- komoditas
- foto
- petani
- lokasi
- harga
- stok
- kualitas
- tanggal panen
- status

#### 6.9 Permintaan Komoditas
Aktor:
- Pengepul
- Konsumen

Field:
- komoditas
- jumlah
- satuan
- harga maksimal
- lokasi
- deadline
- deskripsi
- status

Status:
Aktif, Mendapat Penawaran, Dipenuhi, Ditutup, Dibatalkan.

Petani dapat melihat permintaan relevan dan mengajukan penawaran.

#### 6.10 Penawaran Harga
Aktor:
- Pengepul
- Konsumen
- Petani sebagai penerima/counter party

Field:
- produk
- pembeli
- jumlah
- harga penawaran
- harga produk
- catatan
- status

Status:
Menunggu, Diterima, Ditolak, Counter Offer, Selesai, Dibatalkan.

Kesepakatan menghasilkan order dengan harga yang disetujui.

#### 6.11 Pre-order
Petani dapat membuka pre-order berdasarkan rencana panen.

Field:
- komoditas
- estimasi produksi
- harga
- estimasi tanggal panen
- kapasitas pre-order
- deskripsi

Status:
Dibuka, Menunggu Panen, Siap Diproses, Diproses, Selesai, Dibatalkan.

Rule:
Total pre-order tidak boleh melebihi kapasitas.

#### 6.12 SINTESA Match
Ini adalah fitur pembeda utama.

Input:
- komoditas
- jumlah kebutuhan
- harga maksimal
- lokasi

Output:
- ranking petani/produk
- match score
- kategori kecocokan
- nama petani
- stok
- harga
- lokasi
- jarak

Bobot:
Komoditas 35%
Stok 25%
Harga 20%
Jarak 20%

Score:
`(Commodity × 0.35) + (Stock × 0.25) + (Price × 0.20) + (Distance × 0.20)`

Kategori:
85–100 Sangat Cocok
70–84 Cocok
50–69 Cukup Cocok
0–49 Kurang Cocok

Sumber data harus berasal dari MySQL.
Implementasi pada Laravel Service.

#### 6.13 Transaksi
Sumber:
- direct purchase
- accepted offer
- commodity request
- preorder

Status:
- Menunggu Konfirmasi
- Dikonfirmasi
- Diproses
- Selesai
- Dibatalkan

Flow:
Buyer Order → Farmer Confirmation → Processing → Completed

#### 6.14 Informasi Harga
Data:
- komoditas
- harga
- lokasi
- tanggal
- perubahan harga

Tampilkan:
- harga terbaru
- tertinggi
- terendah
- perubahan
- grafik tren

Admin mengelola data harga.

#### 6.15 Peta Petani
Teknologi:
Leaflet.js + OpenStreetMap.

Marker:
- petani
- komoditas
- produk
- stok
- harga

Filter:
- lokasi
- komoditas

Koordinat disimpan di database.

#### 6.16 Notifikasi
Event:
- order baru
- penawaran baru
- counter offer
- permintaan relevan
- perubahan status transaksi
- perubahan status pre-order

Notifikasi harus berasal dari event nyata.

#### 6.17 Admin
Fitur:
- manajemen pengguna
- manajemen komoditas
- manajemen produk
- manajemen harga
- monitoring transaksi
- monitoring aktivitas

### 7. Database
Tabel utama:
users
farmer_profiles
collector_profiles
consumer_profiles
commodities
harvests
stocks
products
product_images
commodity_requests
request_offers
price_offers
preorders
preorder_items
orders
order_items
transactions
commodity_prices
locations
notifications
reviews

Relasi wajib menggunakan foreign key dan Eloquent relationship.

### 8. Non-Functional Requirements

#### Performance
- gunakan eager loading jika diperlukan
- pagination untuk list besar
- indexing pada kolom pencarian/filter yang relevan
- hindari query berulang/N+1

#### Security
- authentication
- authorization
- role middleware
- Policy
- ownership validation
- CSRF
- server-side validation
- jangan expose data sensitif

#### Responsiveness
Desktop, tablet, mobile.

#### Maintainability
- Service untuk business logic
- Form Request untuk validasi
- Policy untuk authorization
- reusable Blade components
- naming konsisten
- migration dan seeder terdokumentasi

### 9. UI/UX
Tema:
Modern Agriculture + Digital Ecosystem + Data-Driven Platform.

Karakter:
- modern
- bersih
- profesional
- mudah digunakan
- konsisten

Komponen:
- card
- sidebar
- table
- form
- chart
- empty state
- loading state
- error state
- success feedback

Landing page:
Hero → Problem → Solution → How It Works → Features → Ecosystem Statistics → CTA → Footer

### 10. User Journey Utama

#### Journey A — Petani Menjual
Login
→ Catat Panen
→ Stok
→ Buat Produk
→ Produk Aktif
→ Pembeli Melihat
→ Order
→ Petani Konfirmasi
→ Diproses
→ Selesai

#### Journey B — Pembeli Mencari
Login
→ Marketplace
→ Search/Filter
→ Detail Produk
→ Beli / Penawaran / Pre-order
→ Transaksi

#### Journey C — Request
Pengepul/Konsumen
→ Buat Permintaan
→ Sistem menemukan petani relevan
→ Petani memberi penawaran
→ Pembeli memilih
→ Order
→ Transaksi

#### Journey D — SINTESA Match
Input kebutuhan
→ Ambil data MySQL
→ Filter komoditas
→ Cek stok
→ Evaluasi harga
→ Hitung jarak
→ Weighted Score
→ Ranking
→ Rekomendasi

### 11. Acceptance Criteria Utama
Produk dianggap layak jika:
1. seluruh role dapat login dan diarahkan ke dashboard yang sesuai;
2. role tidak dapat mengakses resource yang bukan haknya;
3. petani dapat membuat hasil panen;
4. hasil panen terhubung ke stok;
5. stok dapat menjadi sumber produk marketplace;
6. pembeli dapat mencari dan memfilter produk;
7. order tidak dapat melebihi stok;
8. transaksi memiliki status yang jelas;
9. penawaran dapat diterima/ditolak/counter;
10. pre-order memiliki batas kapasitas;
11. permintaan komoditas dapat direspons petani;
12. SINTESA Match menggunakan data MySQL nyata;
13. score matching mengikuti bobot yang ditetapkan;
14. peta menggunakan koordinat database;
15. harga komoditas dapat dikelola Admin;
16. notifikasi berasal dari aktivitas nyata;
17. dashboard menampilkan data sistem, bukan angka dummy;
18. aplikasi responsive;
19. tidak ada broken page;
20. fitur inti dapat didemokan end-to-end.

### 12. Prioritas Fitur

#### P0 — Wajib untuk demo
- authentication & role
- marketplace
- hasil panen
- stok
- order/transaksi
- SINTESA Match
- penawaran harga
- Admin dasar

#### P1 — Penting
- permintaan komoditas
- pre-order
- peta
- informasi harga
- dashboard analytics
- notifikasi

#### P2 — Polish
- review/rating
- wishlist
- invoice
- advanced analytics
- fitur tambahan non-esensial

### 13. Development Plan
Phase 1: Foundation & Authentication
Phase 2: Core Marketplace
Phase 3: Harvest & Stock
Phase 4: Order & Transaction
Phase 5: Request, Negotiation & Pre-order
Phase 6: SINTESA Match
Phase 7: Maps & Price Analytics
Phase 8: Admin, Testing & Final Polish

### 14. Prinsip Produk
SINTESA harus terasa seperti produk digital nyata, bukan prototype statis.

Prioritas:
1. correctness
2. end-to-end functionality
3. data integrity
4. security
5. maintainability
6. UX
7. visual polish

Lebih baik sedikit fitur tetapi benar-benar bekerja daripada banyak fitur yang hanya berupa tampilan.
