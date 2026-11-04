# Panduan Skenario Demo Aplikasi (UAS - Workshop Pemrograman Framework)

Dokumen ini memuat skenario panduan langkah-demi-langkah demonstrasi langsung (*live demo*) aplikasi **Perpustakaan Digital Kampus (`app-perpustakaan`)** untuk evaluasi Ujian Akhir Semester (UAS) di depan dosen penguji.

---

## I. Persiapan Pra-Demo (*Checklist Wajib*)

Sebelum memanggil dosen penguji atau memulai sesi presentasi:

1. **Jalankan Database MySQL**:
   - Pastikan service MySQL aktif (misalnya via XAMPP Control Panel atau service Windows).
   - Konfigurasi `.env`: `DB_DATABASE=db_perpustakaan`.

2. **Jalankan Dua Instance Server Laravel (Anti-Deadlock Architecture)**:
   - **Terminal 1 (Server Web & UI)**:
     ```bash
     php artisan serve --port=8000
     ```
   - **Terminal 2 (Server Internal API)**:
     ```bash
     php artisan serve --port=8011
     ```
   > **Mengapa Butuh 2 Server?** PHP built-in web server (`php artisan serve`) adalah *single-threaded* (hanya memproses 1 request per waktu). Saat Blade memanggil API internal miliknya sendiri via Laravel HTTP Client (`Http::get()`), pemanggilan ke port yang sama akan menyebabkan *deadlock* (server terkunci). Dengan memisahkan port ke `8011`, request HTTP internal dilayani secara independen dan lancar.

3. **Reset & Seed Database**:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Database otomatis terisi data dummy realistis bergaya Indonesia:*
   - 3 Akun Pengguna (1 Admin, 2 Petugas)
   - 5 Kategori Buku Baku
   - 20 Buku dengan Judul & Penerbit Indonesia Asli
   - 15 Anggota Kampus dengan email `@pens.ac.id`
   - 10 Transaksi Riwayat Peminjaman Lengkap

4. **Kredensial Akun Penguji**:
   | Role | Email | Password | Nama Petugas | Akses Menu |
   |---|---|---|---|---|
   | **Admin** | `admin@pens.ac.id` | `password` | Bambang Sutrisno | Seluruh menu (termasuk Kategori) |
   | **Petugas** | `petugas1@pens.ac.id` | `password` | Siti Rahmawati | Buku, Anggota, Peminjaman, Laporan |
   | **Petugas** | `petugas2@pens.ac.id` | `password` | Ahmad Fauzi | Buku, Anggota, Peminjaman, Laporan |

---

## II. Langkah-Langkah Skenario Live Demo

### 1. Login Petugas / Admin
- **URL**: `http://127.0.0.1:8000/login`
- **Aksi**:
  1. Masukkan email: `admin@pens.ac.id` dan password: `password`.
  2. Klik tombol **Login**.
- **Hasil yang Ditunjukkan**:
  - Berhasil login dan otomatis diarahkan (*redirect*) ke halaman **Dashboard** (`/`).
  - Muncul flash message sukses: *"Login berhasil, selamat datang Bambang Sutrisno."*
  - Navbar menampilkan identitas user: **Bambang Sutrisno (Admin)** beserta tombol Logout.

---

### 2. Tampilkan Dashboard Statistik (Konsumsi `GET /api/stats`)
- **URL**: `http://127.0.0.1:8000/`
- **Hasil yang Ditunjukkan**:
  - Halaman Dashboard menampilkan 3 kartu statistik:
    - **Total Buku**: 20
    - **Total Anggota**: 15
    - **Peminjaman Aktif**: (jumlah transaksi dengan status `dipinjam`)
- **Poin Penjelasan ke Dosen**:
  > *"Angka statistik ini tidak diambil melalui query database langsung di DashboardController, melainkan Controller mengonsumsi endpoint REST API internal `GET /api/stats` menggunakan Laravel HTTP Client (`Http::get`). Logic data sepenuhnya terisolasi di API layer."*

---

### 3. Tambah Kategori Baru (Fitur Khusus Admin)
- **URL**: `http://127.0.0.1:8000/categories`
- **Aksi**:
  1. Klik tombol **+ Tambah Kategori**.
  2. Isi Nama Kategori: `Filsafat`.
  3. Isi Deskripsi: `Buku pengantar filsafat berpikir dan logika komputasi.`
  4. Klik tombol **Simpan**.
- **Hasil yang Ditunjukkan**:
  - Kategori baru berhasil tersimpan dan tampil di tabel daftar kategori dengan flash message sukses.

---

### 4. Tambah Buku Baru Berelasi Kategori
- **URL**: `http://127.0.0.1:8000/books/create`
- **Aksi**:
  1. Isi Judul: `Logika Informatika & Filsafat Komputasi`.
  2. Isi Penulis: `Hafidz Sirajuddin`.
  3. Isi Penerbit: `PENS Press`.
  4. Pilih Kategori: `Filsafat` (kategori yang baru ditambahkan).
  5. Isi Tahun: `2026`, ISBN: `978-623-000-001`, Stok: `5`.
  6. Klik **Simpan**.
- **Hasil yang Ditunjukkan**:
  - Data tersimpan dan tampil di daftar buku dengan nama kategori `Filsafat` (bukan ID angka mentah).

---

### 5. Tambah Anggota Baru & Uji Validasi Unik
- **URL**: `http://127.0.0.1:8000/members/create`
- **Aksi**:
  1. Isi Nama: `Dewi Lestari`, NIM: `3125500099`, Email: `dewi.lestari@pens.ac.id`, Telepon: `081234567890`, Alamat: `Surabaya`.
  2. Klik **Simpan**.
  3. *(Opsional - Uji Validasi)*: Coba buat anggota baru lagi dengan NIM yang sama, tunjukkan validasi Laravel menolak dengan pesan error merah di bawah input form.

---

### 6. Buat Transaksi Peminjaman Baru
- **URL**: `http://127.0.0.1:8000/loans/create`
- **Aksi**:
  1. Pilih Anggota: `Dewi Lestari`.
  2. Pilih Buku (Multi-select checkbox): `Logika Informatika & Filsafat Komputasi`.
  3. Tanggal Pinjam: Hari ini, Tanggal Kembali: 7 hari ke depan.
  4. Klik **Simpan**.
- **Hasil yang Ditunjukkan**:
  - Petugas pencatat (`user_id`) otomatis terisi akun yang sedang login (`Bambang Sutrisno`) tanpa input manual.
  - Redirect ke `/loans` dengan status badge kuning `DIPINJAM`.

---

### 7. Lihat Daftar Peminjaman Aktif & Relasi Eager Loading
- **URL**: `http://127.0.0.1:8000/loans`
- **Hasil yang Ditunjukkan**:
  - Transaksi yang baru dibuat tampil rapi.
  - Nama anggota, nama petugas, dan daftar judul buku tampil utuh berkat relasi Eloquent `with(['member', 'user', 'loanItems.book'])` yang bebas dari masalah *N+1 Query*.

---

### 8. Proses Pengembalian Buku
- **URL**: `http://127.0.0.1:8000/loans`
- **Aksi**:
  1. Pada baris transaksi yang berstatus `DIPINJAM`, klik tombol **Kembalikan**.
- **Hasil yang Ditunjukkan**:
  - Status peminjaman instan berubah menjadi badge hijau `DIKEMBALIKAN`.
  - Tanggal dikembalikan terisi otomatis dengan tanggal hari ini.

---

### 9. Tampilkan Halaman Laporan Peminjaman (Konsumsi `GET /api/loans`)
- **URL**: `http://127.0.0.1:8000/loans/report`
- **Hasil yang Ditunjukkan**:
  - Tabel transaksi pinjam tampil lengkap dengan nomor ID, nama peminjam, nama petugas, buku, tanggal, dan status.
  - Terdapat kontrol navigasi halaman (pagination meta dari API).
- **Poin Penjelasan ke Dosen**:
  > *"Halaman Laporan ini mengonsumsi REST API `GET /api/loans`. Controller menerima array JSON dari API dan mem-parsing data ke Blade view, mendemonstrasikan arsitektur Backend-for-Frontend (BFF) di mana Blade murni bertindak sebagai konsumen data."*

---

### 10. Demonstrasi Pengujian REST API di Postman
Buka aplikasi **Postman** dan jalankan pengujian endpoint berikut:

1. **`GET http://127.0.0.1:8011/api/books`**
   - Header: `Accept: application/json`
   - Status: `200 OK`
   - Tunjukkan struktur response terbungkus API Resource: `data` (berisi list buku dengan kategori objek), `links`, dan `meta` pagination.

2. **`GET http://127.0.0.1:8011/api/stats`**
   - Header: `Accept: application/json`
   - Status: `200 OK`
   - Response:
     ```json
     {
         "total_buku": 21,
         "total_anggota": 16,
         "peminjaman_aktif": 5
     }
     ```
   - Tunjukkan angka-angka ini persis sama dengan yang tampil di kartu halaman Dashboard Blade.

3. **`GET http://127.0.0.1:8011/api/loans`**
   - Header: `Accept: application/json`
   - Status: `200 OK`
   - Tunjukkan response list transaksi lengkap dengan objek nested member, petugas, dan array buku.

---

## III. Kisi-Kisi Tanya Jawab Konseptual UAS

| Pertanyaan Penguji | Inti Jawaban Terbaik |
|---|---|
| **Kenapa Blade memanggil API sendiri alih-alih query langsung ke Eloquent?** | Untuk menerapkan prinsip *Separation of Concerns*. Controller web tidak perlu tahu kompleksitas query/penghitungan data; jika logic API berubah atau aplikasi dibuatkan mobile client di masa depan, endpoint API yang sama langsung siap dipakai tanpa menulis ulang logic. |
| **Kenapa butuh 2 port server berbeda di localhost?** | PHP built-in web server (`php artisan serve`) hanya single-threaded (1 worker). Jika 1 server memproses request web lalu membuat request HTTP ke dirinya sendiri, server akan *deadlock* (saling tunggu). Di server produksi (Nginx + PHP-FPM), banyak request ditangani bersamaan sehingga 1 server sudah cukup. |
| **Apa itu N+1 Query Problem dan bagaimana cara mengatasinya?** | Masalah saat aplikasi menjalankan 1 query untuk mengambil N data induk, lalu menjalankan N query tambahan untuk mengambil relasi masing-masing anak (total 1 + N query). Diatasi dengan teknik *Eager Loading* menggunakan method `with(['relasi'])`. |
| **Apa fungsi API Resource di Laravel?** | Sebagai layer transformasi antara Eloquent Model dan respons JSON. Berfungsi memfilter field sensitif (seperti password), menyeragamkan format nama key (camelCase / snake_case), dan mencegah perubahan struktur database langsung merusak kontrak API dengan klien. |
| **Bagaimana penanganan error jika server API mati?** | Pada `DashboardController` dan `LoanController@report`, pemanggilan `Http::get()` dibungkus dalam blok `try/catch (\Illuminate\Http\Client\ConnectionException $e)`. Jika API mati atau tidak merespons, aplikasi tidak mengalami crash (error 500), melainkan memberikan nilai *fallback* yang aman (angka 0 dan tabel kosong). |
