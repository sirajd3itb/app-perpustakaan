# Sistem Informasi Perpustakaan Digital Kampus (`app-perpustakaan`)

Aplikasi web manajemen perpustakaan modern berbasis **Laravel 12** yang dikembangkan sebagai proyek akhir mata kuliah **Workshop Pemrograman Framework (WPF)** di Program Studi D3 Teknik Informatika, Politeknik Elektronika Negeri Surabaya (PENS).

---

## Identitas Pengembang
- **Nama**: Muhammad Hafidz Sirajuddin
- **NRP**: 3125500047
- **Kelas**: D3 IT B
- **Dosen Pengajar**: Mu'arifin, S.ST., M.T
- **Tahun Ajaran**: 2026/2027

---

## Fitur Utama Aplikasi

1. **Autentikasi & Otorisasi Berbasis Role**:
   - Multi-role: `Admin` (akses penuh termasuk kelola kategori) dan `Petugas` (operasional buku, anggota, peminjaman).
   - Middleware protection (`auth`, `admin`) dan halaman ganti password mandiri.
2. **Katalog & Master Data**:
   - Manajemen Kategori Buku (CRUD lengkap, validasi nama unik).
   - Manajemen Buku (CRUD, relasi kategori, stok dinamis, ISBN unik).
   - Manajemen Anggota (CRUD, format NIM & nomor telepon Indonesia, email domain kampus `@pens.ac.id`).
3. **Sirkulasi Peminjaman & Pengembalian**:
   - Peminjaman multi-buku dalam satu transaksi (`loan_items`).
   - Pencatatan otomatis petugas yang sedang bertugas dari sesi login.
   - Fitur pengembalian satu klik dengan kalkulasi otomatis status (`dipinjam`, `dikembalikan`, `terlambat`).
   - Eager Loading (`with`) pada relasi Eloquent untuk mencegah masalah *N+1 Query Problem*.
4. **REST API Lengkap (11 Endpoint)**:
   - Resource terstruktur via Laravel API Resource (`BookResource`, `MemberResource`, `LoanResource`).
   - Filtering data buku, anggota, peminjaman, serta endpoint statistik agregat (`GET /api/stats`).
   - Lihat dokumentasi teknis lengkap di [API.md](API.md).
5. **Konsumsi API Internal (BFF Architecture)**:
   - **Dashboard (`/`)**: Menampilkan 3 kartu statistik ringkas (`total_buku`, `total_anggota`, `peminjaman_aktif`) yang diambil langsung dari `GET /api/stats` via Laravel HTTP Client (`Http::get()`).
   - **Laporan (`/loans/report`)**: Menampilkan tabel riwayat transaksi peminjaman hasil konsumsi `GET /api/loans` dengan pagination meta.
   - Resilient dengan penanganan *ConnectionException* (fallback nilai default saat server API offline).
6. **Seeder & Factory Data Realistis**:
   - Locale Faker Indonesia (`id_ID`) menghasilkan nama orang dan alamat Indonesia autentik.
   - Kurasi judul buku sastra/teknologi/sains asli serta penerbit terkemuka Indonesia (Gramedia, Mizan, Balai Pustaka, dll.).
7. **Pagination Bersih & Rapi**:
   - Custom Blade pagination tanpa ketergantungan Tailwind CSS, bebas dari glitch rendering SVG raksasa.

---

## Persyaratan Sistem

- PHP >= 8.2
- Composer >= 2.x
- MySQL / MariaDB >= 10.4
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `json`

---

## Panduan Instalasi & Setup

### 1. Clone Repository
```bash
git clone https://github.com/sirajd3itb/app-perpustakaan.git
cd app-perpustakaan
```

### 2. Install Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Environment (`.env`)
Salin berkas `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Pastikan pengaturan database dan URL API internal terkonfigurasi:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_perpustakaan
DB_USERNAME=root
DB_PASSWORD=

# Base URL instance kedua untuk panggilan API internal (Wajib port berbeda)
INTERNAL_API_URL=http://127.0.0.1:8011
APP_FAKER_LOCALE=id_ID
```

Generate application key:
```bash
php artisan key:generate
```

### 4. Migrasi & Seeding Database
Jalankan migrasi seluruh tabel dan isi database dengan data dummy siap pakai:
```bash
php artisan migrate:fresh --seed
```

---

## Cara Menjalankan Aplikasi (*PENTING: Dual-Server Setup*)

Karena `php artisan serve` (PHP built-in server) bersifat *single-threaded*, memanggil API ke port yang sama akan menimbulkan *deadlock*. Oleh karena itu, jalankan **dua server berdampingan** di dua terminal terpisah:

### Terminal 1 — Server Web Utama (Akses Browser):
```bash
php artisan serve --port=8000
```
Buka browser di: **`http://127.0.0.1:8000`**

### Terminal 2 — Server Khusus API Internal:
```bash
php artisan serve --port=8011
```
Server ini khusus melayani request dari `Http::get()` di Dashboard dan Laporan.

---

## Kredensial Akun Bawaan

| Akun | Email | Password | Role | Hak Akses |
|---|---|---|---|---|
| **Admin** | `admin@pens.ac.id` | `password` | admin | Seluruh fitur + Kelola Kategori |
| **Petugas 1** | `petugas1@pens.ac.id` | `password` | petugas | Buku, Anggota, Peminjaman, Laporan |
| **Petugas 2** | `petugas2@pens.ac.id` | `password` | petugas | Buku, Anggota, Peminjaman, Laporan |

---

## Pengujian Otomatis (*Automated Testing*)

Jalankan pengujian unit dan fitur menggunakan PHPUnit:
```bash
php artisan test
```
*Seluruh test feature (Auth, Dashboard API, Loan Report API) terverifikasi 100% lolos.*

---

## Dokumentasi Pendukung
- **[DEMO.md](DEMO.md)**: Skenario langkah-demi-langkah demonstrasi aplikasi dan kisi-kisi tanya jawab saat ujian UAS.
- **[API.md](API.md)**: Dokumentasi 11 endpoint REST API perpustakaan lengkap dengan parameter request dan format response JSON.
