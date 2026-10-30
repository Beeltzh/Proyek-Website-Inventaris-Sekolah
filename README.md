# Sistem Inventaris Sekolah

Aplikasi berbasis web menggunakan **Laravel 12** dan **MySQL** untuk mengelola pencatatan data barang inventaris, kategori barang, serta proses peminjaman dan pengembalian sarana prasarana sekolah secara digital, terpusat, dan dilengkapi rekam jejak audit (*audit trail*).

---

 Role & Hak Akses Pengguna

Sistem ini mendukung dua *role* utama dengan tampilan menu dan hak akses berbeda sesuai perannya:

1. **Admin (Pengelola Inventaris Sekolah)**
   - Mengelola kategori barang (`categories`) — tambah, ubah, *soft delete*, dan pencarian.
   - Mengelola data barang inventaris (`items`) — kode barang, lokasi penyimpanan, stok total & tersedia, kondisi fisik (`Baik`, `Rusak Ringan`, `Rusak Berat`), unggah foto barang, serta *soft delete*.
   - Mengelola transaksi peminjaman (`loans`) — mencatat peminjaman langsung, menyetujui, menolak, memverifikasi pengembalian fisik barang ke dalam `return_logs`, serta *soft delete*.
   - Memantau jejak audit seluruh aktivitas sistem (`activity_logs`) termasuk detail *snapshot* data sebelum dan sesudah perubahan (JSON).
   - Mengelola akun pengguna (`users` — Admin, Guru, Siswa, Staf) lengkap dengan fitur *soft delete*.
   - Mencetak laporan resmi ber-kop sekolah (Print / PDF) dan mengunduh laporan dalam format CSV/Excel.

2. **User (Guru / Siswa / Staf Peminjam)**
   - Melihat katalog dan ketersediaan barang inventaris sekolah beserta detail lokasi dan kondisinya.
   - Mengajukan peminjaman barang secara digital beserta tanggal pinjam, rencana kembali, dan keperluan kegiatan.
   - Mengajukan pengembalian barang yang sedang dipinjam.
   - Memantau status pengajuan serta riwayat peminjaman (`loans`) dan log pengembalian (`return_logs`) miliknya sendiri.

---

 Kredensial Akun Testing (Seeder)

Seluruh akun *seeder* menggunakan kata sandi yang mudah diingat: **`password123`** (juga tersedia tombol *1-Click Demo Login* pada halaman `/login`).

| Role | Nama Pengguna | Kategori | Email | Password |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | Drs. Hendra Wijaya | Pengelola Sarpras | `admin@sekolah.sch.id` | `password123` |
| **User** | Budi Santoso, S.Pd. | Guru Fisika | `guru@sekolah.sch.id` | `password123` |
| **User** | Bryant Pratama | Siswa XII MIPA 1 | `siswa@sekolah.sch.id` | `password123` |
| **User** | Siti Aminah, M.Pd. | Guru Informatika | `siti@sekolah.sch.id` | `password123` |
| **User** | Nadia Putri Lestari | Siswa XI IPS 2 | `nadia@sekolah.sch.id` | `password123` |

---

 Struktur Entitas / Tabel Database

| Nama Tabel | Model | Seeder | Fungsi |
| :--- | :--- | :--- | :--- |
| `users` | `User` | `UserSeeder` | Menyimpan akun pengguna (`admin` & `user`) beserta identitas NIP/NISN, kelas/jabatan, dan *Soft Delete* (`1 Admin + 4 User`). |
| `categories` | `Category` | `CategorySeeder` | Menyimpan kategori barang inventaris seperti Elektronik, Komputer, Alat Lab IPA, Alat Olahraga, Kelas, dan Kesenian (`6 kategori`). |
| `items` | `Item` | `ItemSeeder` | Menyimpan data barang inventaris yang terhubung ke `categories`, stok total, stok tersedia, lokasi, foto, kondisi, dan *Soft Delete* (`12 barang`). |
| `loans` | `Loan` | `LoanSeeder` | Mencatat transaksi peminjaman barang oleh user dengan variasi status: `menunggu_persetujuan`, `dipinjam`, `menunggu_pengembalian`, `dikembalikan`, dan `ditolak` (`9 transaksi`). |
| `return_logs` | `ReturnLog` | `ReturnLogSeeder` | Mencatat detail pengembalian dari transaksi `loans` yang berstatus `dikembalikan`, mencakup waktu kembali, penerima, keterlambatan, kondisi fisik saat kembali, dan catatan (`4 log pengembalian`). |
| `activity_logs` | `ActivityLog` | `ActivityLogSeeder` | Mencatat jejak audit (*audit trail*) atas setiap aktivitas krusial Admin maupun User: otentikasi (`LOGIN`, `LOGIN_FAILED`, `REGISTER`, `LOGOUT`), operasi CRUD (`CREATE`, `UPDATE`, `DELETE`), serta verifikasi peminjaman & pengembalian (`9 log contoh awal`). |

---

 Optimasi Query & Keamanan Sistem

1. **Pencegahan N+1 Query Problem**:
   - Menerapkan *Eager Loading* (`with(['category', 'user', 'approver', 'returnLog'])`) pada seluruh query daftar barang, peminjaman, log pengembalian, dan jejak audit.
   - Menggunakan subquery agregat bawaan Eloquent (`withCount`, `withSum`) pada daftar kategori dan data pengguna untuk performa tinggi tanpa query berulang.

2. **Pengindeksan Database (*Database Indexing*)**:
   - Kolom frekuensi tinggi diberi indeks: `items.nama_barang`, `items.kondisi`, `loans.status`, `loans.rencana_kembali`, `activity_logs.action`, dan `activity_logs.module`.

3. **Integritas Stok & Pencegahan Race Condition**:
   - Pemotongan dan pemulihan stok barang dijalankan dalam *Database Transaction* (`DB::transaction`) dengan *Pessimistic Locking* (`Item::lockForUpdate()`) guna menjamin akurasi stok jika terjadi banyak pengajuan bersamaan.

4. **Keamanan Dasar & Validasi Input**:
   - **Form Request Validation**: Validasi ketat di sisi server (`app/Http/Requests/*`) dengan aturan tipe data, batasan ukuran, dan validasi keunikan berbasis rule ignore.
   - **CSRF Protection**: Seluruh request form dilindungi token CSRF Laravel (`@csrf`).
   - **Rate Limiting Login**: Laravel Breeze membatasi maksimal 5 percobaan login gagal berturut-turut untuk mencegah serangan *brute force*.
   - **Sanitasi Unggah File**: Unggahan foto dibatasi hanya untuk format gambar valid (`jpg, jpeg, png, webp`), ukuran maksimal 2 MB, dan nama file disanitasi dengan pola karakter aman.
   - **Role-Based Authorization**: Middleware [`RoleMiddleware`](app/Http/Middleware/RoleMiddleware.php) memastikan hanya akun ber-role `admin` yang dapat mengakses modul pengelolaan master.
   - **Password Security**: Password di-hash menggunakan algoritma `bcrypt` dan disembunyikan dari serialisasi JSON (`$hidden`).

---

 Standar Konvensi & Kualitas Proyek (Laravel 12)

- **Konvensi Penamaan Bahasa Inggris**: Tabel *plural `snake_case`* (`users`, `categories`, `items`, `loans`, `return_logs`, `activity_logs`), Model *singular `PascalCase`* (`User`, `Category`, `Item`, `Loan`, `ReturnLog`, `ActivityLog`), Controller berakhiran `Controller`, dan *view* Blade terkelompok per modul.
- **Laravel Breeze Authentication**: Mendukung fitur **Login**, **Register**, **Logout**, dan **Profile** (`routes/auth.php`, `AuthenticatedSessionController`, `RegisteredUserController`, `ProfileController`).
- **Soft Deletes (`deleted_at`)**: Diterapkan pada entitas yang dapat dihapus (`User`, `Category`, `Item`, `Loan`, `ReturnLog`) tanpa menghapus riwayat transaksi secara permanen.
- **Eloquent Relationships Murni**: Seluruh relasi antar tabel menggunakan `hasMany`, `belongsTo`, dan `hasOne` (tanpa *raw SQL `JOIN`*).
- **Form Request Validation & UX Form**: Validasi backend menggunakan *Form Request Validation*, tanda bintang (`*`) pada kolom wajib, `placeholder` di seluruh input, serta fokus otomatis (`autofocus`) ke field pertama pada halaman maupun dialog modal.
- **Format Angka & Tanggal Indonesia**: Pemisah ribuan titik (`.` melalui `format_number()`) dan tanggal berbahasa Indonesia seperti `1 Feb 2026` (melalui `format_date()` & `format_datetime()`).
- **Tabel Responsif, Breadcrumbs & Pagination**: Dilengkapi *search/filter*, *breadcrumbs* konsisten di setiap halaman, serta *pagination* bawaan Laravel.

---

 Panduan Instalasi & Menjalankan Proyek

1. **Persiapan Dependensi & Environment**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan storage:link
   ```

2. **Konfigurasi Database MySQL di `.env`**
   Pastikan layanan MySQL (misalnya melalui XAMPP) sudah aktif dan sesuaikan pengaturan pada file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_inventaris_sekolah
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Jalankan Migrasi & Seeder**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Jalankan Server Aplikasi**
   ```bash
   php artisan serve
   ```
   Buka browser dan akses **http://127.0.0.1:8000**.

5. **Menjalankan Pengujian Otomatis (PHPUnit)**
   ```bash
   php artisan test
   ```
