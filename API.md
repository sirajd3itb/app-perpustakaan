# Dokumentasi REST API - Sistem Perpustakaan (`app-perpustakaan`)
**Pertemuan 9 - REST API Dasar**  
**Mahasiswa:** Muhammad Hafidz Sirajuddin (3125500047)  
**Kelas:** D3 IT B  
**Dosen Pengampu:** Mu'arifin, S.ST., M.T.  

Seluruh endpoint REST API di bawah ini menggunakan prefix `/api`, bersifat *stateless*, mengembalikan response JSON dengan header `Content-Type: application/json`, serta diformat secara terstruktur menggunakan Laravel API Resource.

---

## Ringkasan 11 Endpoint API

| No | Method | Endpoint URL | Status Code | Keterangan & Body Request |
|:--:|:------:|:-------------|:-----------:|:--------------------------|
| 1 | `GET` | `/api/books` | `200 OK` | Daftar buku (Resource Collection + Pagination). Mendukung query `?judul=` dan `?category_id=`. |
| 2 | `GET` | `/api/books/{id}` | `200 OK` / `404 Not Found` | Detail data satu buku beserta kategori nested. |
| 3 | `POST` | `/api/books` | `201 Created` / `422 Unprocessable` | Tambah buku baru (memvalidasi field via `StoreBookRequest`). |
| 4 | `PUT` | `/api/books/{id}` | `200 OK` / `404 Not Found` | Memperbarui data buku yang ada. |
| 5 | `DELETE` | `/api/books/{id}` | `200 OK` / `404 Not Found` | Menghapus data buku. |
| 6 | `GET` | `/api/members` | `200 OK` | Daftar seluruh anggota perpustakaan (Pagination). |
| 7 | `GET` | `/api/members/{id}` | `200 OK` / `404 Not Found` | Detail satu anggota perpustakaan. |
| 8 | `GET` | `/api/loans` | `200 OK` | Daftar transaksi peminjaman (Eager load `member`, `user`, dan `books`). |
| 9 | `POST` | `/api/loans` | `201 Created` / `422 Unprocessable` | Membuat transaksi peminjaman baru (bisa multi-buku via `book_ids[]`). |
| 10 | `PUT` | `/api/loans/{id}/kembalikan` | `200 OK` / `404 Not Found` | Mengembalikan buku pinjaman (`status: dikembalikan`, `tanggal_dikembalikan: hari ini`). |
| 11 | `GET` | `/api/stats` | `200 OK` | Ringkasan statistik perpustakaan (`total_buku`, `total_anggota`, `peminjaman_aktif`). |

---

## Rincian Dokumentasi & Contoh Response Riil

### 1. `GET /api/books`
Mengambil koleksi data buku dengan pagination. Mendukung filter:
- `?judul=laskar` (Pencarian nama buku parsial)
- `?category_id=2` (Filter kategori spesifik)

**Header Request:**
```http
Accept: application/json
```

**Contoh Response (`200 OK`):**
```json
{
  "data": [
    {
      "id": 2,
      "judul": "Laskar Pelangi",
      "penulis": "Andrea Hirata",
      "penerbit": "Bentang Pustaka",
      "tahun_terbit": "2005",
      "isbn": null,
      "stok": 1,
      "sampul": null,
      "category": {
        "id": 2,
        "nama_kategori": "fiksi"
      },
      "created_at": "2026-09-27T09:48:09.000000Z",
      "updated_at": "2026-09-27T09:48:09.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/books?page=1",
    "last": "http://localhost/api/books?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/books?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/books",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 2. `GET /api/books/{id}`
Mengambil detail satu buku berdasarkan ID.

**Contoh Response Sukses (`200 OK`):**
```json
{
  "data": {
    "id": 2,
    "judul": "Laskar Pelangi",
    "penulis": "Andrea Hirata",
    "penerbit": "Bentang Pustaka",
    "tahun_terbit": "2005",
    "isbn": null,
    "stok": 1,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "fiksi"
    },
    "created_at": "2026-09-27T09:48:09.000000Z",
    "updated_at": "2026-09-27T09:48:09.000000Z"
  }
}
```

**Contoh Response Jika ID Tidak Ditemukan (`404 Not Found`):**
```json
{
  "message": "No query results for model [App\\Models\\Book] 999",
  "exception": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
  "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Exceptions\\Handler.php",
  "line": 773,
  "trace": [
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Exceptions\\Handler.php",
      "line": 721,
      "function": "prepareException",
      "class": "Illuminate\\Foundation\\Exceptions\\Handler",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\nunomaduro\\collision\\src\\Adapters\\Laravel\\ExceptionHandler.php",
      "line": 55,
      "function": "render",
      "class": "Illuminate\\Foundation\\Exceptions\\Handler",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Pipeline.php",
      "line": 51,
      "function": "render",
      "class": "NunoMaduro\\Collision\\Adapters\\Laravel\\ExceptionHandler",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 182,
      "function": "handleException",
      "class": "Illuminate\\Routing\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Middleware\\SubstituteBindings.php",
      "line": 52,
      "function": "{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Routing\\Middleware\\SubstituteBindings",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 137,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
      "line": 821,
      "function": "then",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
      "line": 800,
      "function": "runRouteWithinStack",
      "class": "Illuminate\\Routing\\Router",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
      "line": 764,
      "function": "runRoute",
      "class": "Illuminate\\Routing\\Router",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Routing\\Router.php",
      "line": 753,
      "function": "dispatchToRoute",
      "class": "Illuminate\\Routing\\Router",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
      "line": 200,
      "function": "dispatch",
      "class": "Illuminate\\Routing\\Router",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 180,
      "function": "{closure:Illuminate\\Foundation\\Http\\Kernel::dispatchToRouter():197}",
      "class": "Illuminate\\Foundation\\Http\\Kernel",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php",
      "line": 21,
      "function": "{closure:Illuminate\\Pipeline\\Pipeline::prepareDestination():178}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull.php",
      "line": 31,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\ConvertEmptyStringsToNull",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest.php",
      "line": 21,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\TrimStrings.php",
      "line": 51,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\TransformsRequest",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\TrimStrings",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePostSize.php",
      "line": 27,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Http\\Middleware\\ValidatePostSize",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance.php",
      "line": 110,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\PreventRequestsDuringMaintenance",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\HandleCors.php",
      "line": 74,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Http\\Middleware\\HandleCors",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\TrustProxies.php",
      "line": 58,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Http\\Middleware\\TrustProxies",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks.php",
      "line": 22,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Middleware\\InvokeDeferredCallbacks",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Http\\Middleware\\ValidatePathEncoding.php",
      "line": 28,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 219,
      "function": "handle",
      "class": "Illuminate\\Http\\Middleware\\ValidatePathEncoding",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php",
      "line": 137,
      "function": "{closure:{closure:Illuminate\\Pipeline\\Pipeline::carry():194}:195}",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
      "line": 175,
      "function": "then",
      "class": "Illuminate\\Pipeline\\Pipeline",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Http\\Kernel.php",
      "line": 144,
      "function": "sendRequestThroughRouter",
      "class": "Illuminate\\Foundation\\Http\\Kernel",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\test_endpoints.php",
      "line": 20,
      "function": "handle",
      "class": "Illuminate\\Foundation\\Http\\Kernel",
      "type": "->"
    },
    {
      "file": "C:\\Users\\hafid\\Documents\\coolyeah\\s3\\Workshop Pemrograman Framework\\app-perpustakaan\\test_endpoints.php",
      "line": 47,
      "function": "testRequest"
    }
  ]
}
```

---

### 3. `POST /api/books`
Menambahkan data buku baru ke perpustakaan.

**Request Body (`application/json`):**
```json
{
  "judul": "Clean Architecture",
  "penulis": "Robert C. Martin",
  "penerbit": "Prentice Hall",
  "tahun_terbit": 2018,
  "isbn": "978-0134494166",
  "stok": 5,
  "category_id": 2
}
```

**Contoh Response Sukses (`201 Created`):**
```json
{
  "data": {
    "id": 3,
    "judul": "Clean Architecture",
    "penulis": "Robert C. Martin",
    "penerbit": "Prentice Hall",
    "tahun_terbit": 2018,
    "isbn": "978-0134494166",
    "stok": 5,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "fiksi"
    },
    "created_at": "2026-11-02T09:22:20.000000Z",
    "updated_at": "2026-11-02T09:22:20.000000Z"
  }
}
```

**Contoh Response Gagal Validasi (`422 Unprocessable Entity`):**
```json
{
  "message": "Judul buku wajib diisi. (and 5 more errors)",
  "errors": {
    "judul": [
      "Judul buku wajib diisi."
    ],
    "penulis": [
      "Nama penulis wajib diisi."
    ],
    "penerbit": [
      "Nama penerbit wajib diisi."
    ],
    "tahun_terbit": [
      "Tahun terbit wajib diisi."
    ],
    "stok": [
      "Stok wajib diisi."
    ],
    "category_id": [
      "Kategori wajib dipilih."
    ]
  }
}
```

---

### 4. `PUT /api/books/{id}`
Memperbarui informasi buku berdasarkan ID.

**Request Body (`application/json`):**
```json
{
  "judul": "Clean Architecture - Revised Edition",
  "penulis": "Robert C. Martin",
  "penerbit": "Pearson",
  "tahun_terbit": 2020,
  "isbn": "978-0134494166",
  "stok": 8,
  "category_id": 2
}
```

**Contoh Response (`200 OK`):**
```json
{
  "data": {
    "id": 3,
    "judul": "Clean Architecture - Revised Edition",
    "penulis": "Robert C. Martin",
    "penerbit": "Pearson",
    "tahun_terbit": 2020,
    "isbn": "978-0134494166",
    "stok": 8,
    "sampul": null,
    "category": {
      "id": 2,
      "nama_kategori": "fiksi"
    },
    "created_at": "2026-11-02T09:22:20.000000Z",
    "updated_at": "2026-11-02T09:22:20.000000Z"
  }
}
```

---

### 5. `DELETE /api/books/{id}`
Menghapus data buku dari sistem.

**Contoh Response (`200 OK`):**
```json
{
  "message": "Buku berhasil dihapus."
}
```

---

### 6. `GET /api/members`
Mengambil daftar anggota perpustakaan secara terpaginasi.

**Contoh Response (`200 OK`):**
```json
{
  "data": [
    {
      "id": 2,
      "nama": "Hafidz",
      "nim": "047",
      "email": "123@gmail.com",
      "nomor_telepon": "08123456789",
      "alamat": "rumah",
      "status": "aktif",
      "created_at": "2026-09-27T09:51:17.000000Z",
      "updated_at": "2026-09-27T09:51:17.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/members?page=1",
    "last": "http://localhost/api/members?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/members?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/members",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

---

### 7. `GET /api/members/{id}`
Mengambil data detail satu anggota.

**Contoh Response (`200 OK`):**
```json
{
  "data": {
    "id": 2,
    "nama": "Hafidz",
    "nim": "047",
    "email": "123@gmail.com",
    "nomor_telepon": "08123456789",
    "alamat": "rumah",
    "status": "aktif",
    "created_at": "2026-09-27T09:51:17.000000Z",
    "updated_at": "2026-09-27T09:51:17.000000Z"
  }
}
```

---

### 8. `GET /api/loans`
Menampilkan riwayat dan daftar transaksi peminjaman dengan relasi `member`, `user` (petugas), dan array `books`.

**Contoh Response (`200 OK`):**
```json
{
  "data": [],
  "links": {
    "first": "http://localhost/api/loans?page=1",
    "last": "http://localhost/api/loans?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": null,
    "last_page": 1,
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "page": null,
        "active": false
      },
      {
        "url": "http://localhost/api/loans?page=1",
        "label": "1",
        "page": 1,
        "active": true
      },
      {
        "url": null,
        "label": "Next &raquo;",
        "page": null,
        "active": false
      }
    ],
    "path": "http://localhost/api/loans",
    "per_page": 10,
    "to": null,
    "total": 0
  }
}
```

---

### 9. `POST /api/loans`
Membuat transaksi peminjaman baru.

**Request Body (`application/json`):**
```json
{
  "member_id": 2,
  "user_id": 1,
  "tanggal_pinjam": "2026-11-02",
  "tanggal_kembali": "2026-11-09",
  "book_ids": [2]
}
```

**Contoh Response Sukses (`201 Created`):**
```json
{
  "data": {
    "id": 1,
    "member": {
      "id": 2,
      "nama": "Hafidz"
    },
    "petugas": {
      "id": 1,
      "name": "Admin Perpustakaan"
    },
    "tanggal_pinjam": "2026-11-02",
    "tanggal_kembali": "2026-11-09",
    "tanggal_dikembalikan": null,
    "status": "dipinjam",
    "books": [
      {
        "id": 2,
        "judul": "Laskar Pelangi"
      }
    ]
  }
}
```

---

### 10. `PUT /api/loans/{id}/kembalikan`
Memproses pengembalian buku pinjaman.

**Contoh Response (`200 OK`):**
```json
{
  "data": {
    "id": 1,
    "member": {
      "id": 2,
      "nama": "Hafidz"
    },
    "petugas": {
      "id": 1,
      "name": "Admin Perpustakaan"
    },
    "tanggal_pinjam": "2026-11-02",
    "tanggal_kembali": "2026-11-09",
    "tanggal_dikembalikan": "2026-11-02",
    "status": "dikembalikan",
    "books": [
      {
        "id": 2,
        "judul": "Laskar Pelangi"
      }
    ]
  }
}
```

---

### 11. `GET /api/stats`
Mengambil ringkasan statistik operasional perpustakaan.

**Contoh Response (`200 OK`):**
```json
{
  "total_buku": 1,
  "total_anggota": 1,
  "peminjaman_aktif": 0
}
```
