# 📚 Panduan Integrasi 4 Modul Sistem Terpadu PKL

Dokumen ini adalah panduan lengkap bagi seluruh anggota tim (**Absensi, Chatbot WA, Pelaporan & Pemeliharaan Alat, dan Penyewaan Reklame**) untuk menggabungkan kodingan ke dalam satu repositori Laravel tanpa saling bentrok (*merge conflict*).

---

## 🏛️ Konsep Dasar Sistem

1. **Satu Repositori & Satu Aplikasi Laravel**: Seluruh modul berjalan dalam satu instalasi Laravel.
2. **Satu Pintu Masuk (Single Auth)**: Login dilakukan di halaman utama (`/login`). Pengguna tinggal memilih modul yang ingin dituju pada sisi kiri layar, lalu memasukkan email & password untuk langsung masuk ke dashboard modul tersebut.
3. **Wadah Mandiri**: Setiap modul memiliki folder **Controller**, **Views**, dan file **Routes** sendiri. Jangan mengubah berkas di luar folder modul Anda sendiri.

---

## 🗂️ Peta Folder Kerja Tiap Modul

| Modul | Pengembang | Folder Controller | Folder Views | File Routes | Alamat Akses (URL) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Absensi** | Naufal | `app/Http/Controllers/Absensi/` | `resources/views/absensi/` | `routes/absensi.php` | `/karyawan` & `/admin` |
| **Chatbot WA** | Aron | `app/Http/Controllers/ChatbotWa/` | `resources/views/chatbotwa/` | `routes/chatbotwa.php` | `/chatbotwa` |
| **Pelaporan & Pemeliharaan Alat** | Adi | `app/Http/Controllers/PemeliharaanAlat/` | `resources/views/pemeliharaanalat/` | `routes/pemeliharaanalat.php` | `/pemeliharaanalat` |
| **Penyewaan Reklame** | Yanor | `app/Http/Controllers/PenyewaanReklame/` | `resources/views/penyewaanreklame/` | `routes/penyewaanreklame.php` | `/penyewaanreklame` |

---

## ⚙️ Berkas Bersama (DILARANG Diubah Sembarangan)

Untuk menjaga kestabilan aplikasi bersama, jangan mengubah file berikut:
- `app/Http/Controllers/AuthController.php` & `Controller.php` (pusat otentikasi)
- `resources/views/login.blade.php` (halaman login bersama)
- `bootstrap/app.php` (konfigurasi routing dan middleware global)
- Tabel utama `users`

---

## 🚀 Langkah Demi Langkah Memindahkan Projek Anda

### 1. Memindahkan Controller
Letakkan seluruh Controller fitur Anda di dalam folder Controller masing-masing:
* **Chatbot WA**: `app/Http/Controllers/ChatbotWa/`
* **Pemeliharaan Alat**: `app/Http/Controllers/PemeliharaanAlat/`
* **Penyewaan Reklame**: `app/Http/Controllers/PenyewaanReklame/`

> **PENTING (Namespace):** Pastikan baris paling atas controller Anda menggunakan namespace foldernya:
> ```php
> namespace App\Http\Controllers\ChatbotWa; // Sesuaikan dengan modul Anda
> 
> use App\Http\Controllers\Controller;
> use Illuminate\Http\Request;
> 
> class PesanController extends Controller
> {
>     // ...
> }
> ```

---

### 2. Memindahkan Tampilan (Views / Blade)
Letakkan file `.blade.php` Anda di dalam folder view masing-masing:
* **Chatbot WA**: `resources/views/chatbotwa/`
* **Pemeliharaan Alat**: `resources/views/pemeliharaanalat/`
* **Penyewaan Reklame**: `resources/views/penyewaanreklame/`

Setiap modul sudah disiapkan layout mandiri di:
`resources/views/<nama_modul>/layouts/app.blade.php`

**Cara pemanggilan view dari Controller:**
Selalu sertakan nama folder modul Anda di awal:
```php
// Contoh di modul Chatbot WA:
return view('chatbotwa.dashboard');
return view('chatbotwa.riwayat');

// Contoh di modul Pemeliharaan Alat:
return view('pemeliharaanalat.dashboard');
return view('pemeliharaanalat.lapor');

// Contoh di modul Reklame:
return view('penyewaanreklame.dashboard');
```

---

### 3. Mendaftarkan Route (URL)
**JANGAN** mengedit file `routes/web.php` atau `routes/absensi.php`. Cukup buka file route modul Anda sendiri:
* **Chatbot WA**: `routes/chatbotwa.php`
* **Pemeliharaan Alat**: `routes/pemeliharaanalat.php`
* **Penyewaan Reklame**: `routes/penyewaanreklame.php`

Semua route di file tersebut **sudah otomatis** diberi:
1. **Prefix URL**: misal `/chatbotwa/...`
2. **Prefix Nama Route**: misal `route('chatbotwa.dashboard')`
3. **Middleware Keamanan**: Pengguna wajib login terlebih dahulu (`role:karyawan,manajer`)

**Contoh isi `routes/chatbotwa.php`:**
```php
use App\Http\Controllers\ChatbotWa\DashboardController;
use App\Http\Controllers\ChatbotWa\PesanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/pesan', [PesanController::class, 'index'])->name('pesan.index');
Route::post('/pesan/kirim', [PesanController::class, 'kirim'])->name('pesan.kirim');
```
*Hasil URL di browser otomatis menjadi:* `http://localhost:8000/chatbotwa/pesan`

---

### 4. Database & Migration
1. Letakkan file migration Anda di `database/migrations/`.
2. **Pencegahan Bentrok Nama Tabel:**
   Gunakan awalan atau nama tabel yang jelas dan spesifik modul Anda, contoh:
   * Chatbot WA: `wa_messages`, `wa_contacts`, `wa_templates`
   * Pemeliharaan Alat: `alat_rusak`, `alat_kategori`, `perawatan_logs`
   * Penyewaan Reklame: `reklame_titik`, `reklame_sewa`, `reklame_klien`
3. **Mengaitkan Data ke User yang Login:**
   Jika tabel Anda butuh pencatatan siapa user yang menginput:
   ```php
   $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
   ```
4. **Model:** Letakkan file model Anda di `app/Models/`.

---

## 🔑 Pengujian & Akun Demo
Setelah memasang kodingan, jalankan server lokal:
```bash
php artisan serve
```
Buka browser di `http://127.0.0.1:8000/login`:
1. Klik salah satu layanan di sebelah kiri (misal: **Chatbot WA**).
2. Klik tombol demo **Karyawan** atau **Manajer** di sisi kanan bawah.
3. Anda akan otomatis langsung masuk ke halaman utama modul Anda (`/<nama_modul>`).
