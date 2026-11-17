# 📚 Panduan Integrasi 4 Modul Sistem PKL

Selamat datang! Repositori ini telah dipersiapkan dengan arsitektur **Modular Terpadu** sehingga 4 anggota kelompok (**Naufal, Aron, Yanor, dan Adi**) dapat menggabungkan sistem masing-masing tanpa takut bentrok file / conflict.

---

## 🗂️ Pembagian Wadah / Folder Kerja

Masing-masing anggota memiliki wadah khusus untuk **Controller**, **Views**, dan **Routes**:

| Anggota / Modul | Folder Controller | Folder Views | File Routes | Akses URL |
| :--- | :--- | :--- | :--- | :--- |
| **Naufal / Nafmi** (Presensi) | `app/Http/Controllers/` | `resources/views/karyawan/` & `admin/` | `routes/web.php` | `/karyawan` & `/admin` |
| **Aron** | `app/Http/Controllers/Aron/` | `resources/views/aron/` | `routes/aron.php` | `/aron` |
| **Yanor** | `app/Http/Controllers/Yanor/` | `resources/views/yanor/` | `routes/yanor.php` | `/yanor` |
| **Adi** | `app/Http/Controllers/Adi/` | `resources/views/adi/` | `routes/adi.php` | `/adi` |

---

## 🚀 Cara Teman Kamu Memindahkan Projeknya

### 1. Memindahkan Controller
* Letakkan semua file Controller ke subfolder masing-masing.
  * Aron: `app/Http/Controllers/Aron/`
  * Yanor: `app/Http/Controllers/Yanor/`
  * Adi: `app/Http/Controllers/Adi/`
* Pastikan baris pertama controller memiliki namespace yang sesuai, contoh:
  ```php
  namespace App\Http\Controllers\Aron;

  use App\Http\Controllers\Controller;
  ```

### 2. Memindahkan Views (Blade)
* Letakkan seluruh file `.blade.php` ke folder masing-masing:
  * Aron: `resources/views/aron/`
  * Yanor: `resources/views/yanor/`
  * Adi: `resources/views/adi/`
* Di dalam controller saat merender view, panggil dengan prefix foldernya:
  ```php
  return view('aron.nama_file'); // untuk Aron
  return view('yanor.nama_file'); // untuk Yanor
  return view('adi.nama_file'); // untuk Adi
  ```

### 3. Memindahkan Routes (URL)
* **JANGAN** mengedit langsung `routes/web.php` agar tidak bentrok.
* Tulis semua route milik masing-masing di file terpisah:
  * Aron: edit `routes/aron.php`
  * Yanor: edit `routes/yanor.php`
  * Adi: edit `routes/adi.php`
* Semua route di dalam file tersebut otomatis mendapat prefix URL dan nama, contoh:
  ```php
  // Di dalam routes/aron.php:
  Route::get('/barang', [BarangController::class, 'index'])->name('barang');
  // URL jadinya: http://localhost:8000/aron/barang
  // Route name: route('aron.barang')
  ```

### 4. Database & Migration
* Letakkan file migration di `database/migrations/`.
* **Tips Aman:** Berikan prefix nama tabel agar tidak tertukar, misalnya:
  * Tabel Aron: `aron_barang`, `aron_kategori`, dll.
  * Tabel Yanor: `yanor_distribusi`, dll.
  * Tabel Adi: `adi_penilaian`, dll.
* Model diletakkan di `app/Models/`.

### 5. Autentikasi Bersama (Single Auth)
* Sistem autentikasi (Login/Logout) sudah berada di `app/Http/Controllers/AuthController.php`.
* Pengguna yang sudah login dapat membuka menu utama di:
  👉 **`http://localhost:8000/portal`** (Portal Modul Terpadu)
* Dari halaman Portal tersebut, pengguna bisa langsung klik untuk masuk ke Modul Naufal, Aron, Yanor, atau Adi.

