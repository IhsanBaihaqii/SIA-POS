## README.md untuk SIA-POS

# SIA-POS (Sistem Informasi Akuntansi - Point of Sale)

SIA-POS adalah aplikasi web berbasis PHP untuk mengelola sistem informasi akuntansi sederhana dengan fitur point of sale (POS). Aplikasi ini mencakup manajemen pelanggan, barang/stok, transaksi penjualan, beban, laporan keuangan, dan multi-user dengan role admin & staff.

Dibangun dengan **PHP native**, **Tailwind CSS**, dan **Font Awesome** untuk tampilan yang modern dan responsif. Database menggunakan MySQL.

---

## ✨ Fitur Utama

- **Login & Manajemen User**
  - Role: Admin dan Staff
  - Password disimpan plaintext (siap di-hash manual)

- **Dashboard**
  - Ringkasan pendapatan, beban, laba/rugi
  - Statistik stok minimum, transaksi terbaru

- **Pelanggan**
  - CRUD pelanggan (nama, no HP, alamat)
  - Pencarian & filter

- **Stok / Barang**
  - Kategori: produk, bahan, kemasan, lainnya
  - Stok masuk & keluar dengan riwayat mutasi
  - Pencarian otomatis

- **Transaksi Penjualan**
  - Pilih pelanggan & barang dengan autocomplete (AJAX)
  - Keranjang belanja interaktif
  - Status pembayaran: menunggu, lunas, sebagian, batal
  - Pembayaran dengan kembalian otomatis
  - Tanggal transaksi dapat disesuaikan

- **Beban Operasional**
  - Catat beban seperti gaji, listrik, dll.

- **Laporan**
  - Laba/Rugi per periode
  - Pendapatan (transaksi lunas)
  - Laporan stok
  - Ekspor ke CSV/Excel

---

## 🛠️ Teknologi

- **Backend**: PHP 7.4+ (PDO, session, AJAX endpoint)
- **Frontend**: HTML5, Tailwind CSS (CDN), Font Awesome, JavaScript (fetch API)
- **Database**: MySQL / MariaDB

---

## 📁 Struktur Folder

```
SIA-POS/
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
├── config/
│   ├── database.php
│   └── auth.php
├── includes/
│   ├── header.php
│   ├── sidebar.php
│   ├── navbar.php
│   ├── footer.php
│   └── functions.php
├── pages/
│   ├── dashboard.php
│   ├── pelanggan/
│   ├── barang/
│   ├── transaksi/
│   ├── beban/
│   ├── laporan/
│   └── user/
├── ajax/
│   ├── search_pelanggan.php
│   └── search_barang.php
├── index.php
├── login.php
├── logout.php
└── .htaccess
```

---

## 🗄️ Database

Database: `sia_db`

**Tabel utama:**

- `users` – akun pengguna
- `pelanggan` – data pelanggan
- `barang` – master barang dan stok
- `stok_mutasi` – riwayat stok masuk/keluar
- `transaksi` – header transaksi penjualan
- `detail_transaksi` – detail item yang dibeli
- `beban` – pengeluaran operasional

**Relasi antar tabel** dapat dilihat pada file SQL di bawah.

---

## 🚀 Instalasi

1. **Clone repository**

   ```bash
   git clone https://github.com/IhsanBaihaqii/SIA-POS.git
   ```

2. **Buat database**  
   Import file `database.sql` (jika tersedia) atau jalankan perintah SQL berikut:

   ```sql
   CREATE DATABASE sia_db;
   -- lalu import struktur tabel sesuai dokumentasi
   ```

   > Jika belum ada file SQL, buat tabel sesuai dengan dokumentasi di atas atau gunakan phpMyAdmin.

3. **Konfigurasi koneksi database**  
   Edit file `config/database.php`:

   ```php
   $host = 'localhost';
   $dbname = 'sia_db';
   $username = 'root';
   $password = '';
   ```

4. **Jalankan aplikasi**  
   Letakkan folder proyek di dalam direktori web server (misal `htdocs` untuk XAMPP).  
   Akses melalui browser: `http://localhost/SIA-POS`

5. **Login**  
   Gunakan akun default (lihat bagian Kredensial Default di bawah).

---

## 🔐 Kredensial Default

| Role  | Username | Password |
| ----- | -------- | -------- |
| Admin | admin    | admin    |
| Staff | staff    | staff    |

> Ganti password setelah login pertama. Password tidak di-hash agar memudahkan pengujian awal.

---

## 📝 Penggunaan

- **Transaksi Penjualan**
  - Buka menu **Transaksi → Buat Transaksi**
  - Pilih pelanggan (ketik nama, pilih dari dropdown)
  - Cari barang, atur jumlah, klik tambah
  - Total otomatis dihitung
  - Simpan transaksi, stok barang berkurang otomatis

- **Pembayaran**
  - Buka detail transaksi
  - Jika status `menunggu`, masukkan jumlah bayar
  - Jika jumlah >= total, status menjadi `lunas` dan kembalian dihitung

- **Laporan**
  - Menu **Laporan** → pilih jenis laporan
  - Filter berdasarkan tanggal
  - Klik tombol **Export** untuk mengunduh CSV

---

## 🤝 Kontribusi

Silakan fork repository ini dan buat pull request untuk perbaikan atau penambahan fitur.

---

## 📧 Kontak

Dikembangkan oleh [Ihsan Baihaqi](https://github.com/IhsanBaihaqii)  
Jika ada pertanyaan, silakan buka issue di repository ini.

---

**Catatan:**

- Sesuaikan bagian `database.sql` jika Anda akan menyertakan file dump database.
- Jika ingin menambahkan screenshot, buat folder `docs/img` dan sisipkan dengan sintaks markdown `![alt](path)`.
