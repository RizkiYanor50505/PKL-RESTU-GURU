# Rencana Pengerjaan Sistem Absensi Karyawan (Web)

**Konsep:** Absensi dengan scan QR Code statis, divalidasi GPS dan IP publik di server, dengan waktu server. Dilengkapi pengajuan izin (tidak masuk/terlambat dengan foto bukti) dan dashboard admin.

---

## 1. Ringkasan Proyek

| Item | Keputusan |
|---|---|
| Platform | Web responsif (mobile-first), diakses lewat browser HP |
| QR Code | **Statis** (token acak per lokasi, dapat dirotasi/dicabut admin) |
| Validasi GPS | Di **server** (rumus Haversine + cek akurasi) |
| Validasi jaringan | **Cek IP publik** di server |
| Waktu | **Waktu server** |
| Tingkat keamanan | **Tinggi** (semua lapisan validasi wajib lolos) |
| Selfie | Tidak digunakan |
| Stack yang disarankan | Laravel 11, MySQL/PostgreSQL, Tailwind, library scan QR JS (`html5-qrcode`), HTTPS |

## 2. Ruang Lingkup

### Fitur Karyawan
- Login dan logout
- Scan QR untuk absen masuk/pulang
- Riwayat absensi
- Pengajuan izin: tidak masuk (sakit/izin/cuti) dan terlambat, dengan unggah foto bukti
- Pantau status pengajuan (menunggu/disetujui/ditolak)

### Fitur Admin
- Monitoring kehadiran real-time (hadir, terlambat, izin, belum absen)
- Detail absensi: koordinat, jarak dari kantor, IP, perangkat, status validasi
- Daftar anomali dan percobaan absen gagal
- Persetujuan/penolakan pengajuan izin
- Master data: karyawan, divisi, shift, lokasi, hari libur
- Pengelolaan QR: cetak, rotasi token, cabut token
- Reset perangkat karyawan
- Laporan harian/bulanan dan ekspor Excel/PDF

### Di Luar Lingkup (Tahap Lanjut)
- QR dinamis
- Face recognition/liveness
- Aplikasi native
- Integrasi payroll
- Mode offline

---

## 3. Prasyarat Sebelum Mulai

- [ ] Pastikan kantor punya **IP publik statis** (hubungi ISP jika belum). Ini syarat kunci cek IP
- [ ] Daftar semua lokasi kantor beserta koordinat GPS dan IP publiknya
- [ ] Domain dan sertifikat **HTTPS** (wajib untuk akses kamera dan GPS di browser)
- [ ] Server/hosting (VPS) dengan PHP 8.2+, MySQL, dan Composer
- [ ] Daftar karyawan, divisi, dan jam kerja/shift
- [ ] Kebijakan privasi dan persetujuan (consent) karyawan untuk data lokasi (UU PDP)
- [ ] Aturan perusahaan: toleransi keterlambatan, definisi alpa, jenis cuti

---

## 4. Rancangan Teknis

### 4.1 Lapisan Validasi Absensi (Semua Wajib Lolos)

| # | Validasi | Tempat | Hasil jika gagal |
|---|---|---|---|
| 1 | Token QR cocok dengan lokasi aktif | Server | Ditolak |
| 2 | Pengguna sudah login, sesi valid, CSRF aman | Server | Ditolak |
| 3 | Perangkat terdaftar (device binding) | Server | Ditolak |
| 4 | IP publik termasuk daftar IP kantor | Server | Ditolak |
| 5 | Akurasi GPS <= 50 m | Server | Minta coba ulang |
| 6 | Jarak ke kantor <= radius (misal 100 m) | Server | Ditolak |
| 7 | Belum absen untuk tipe/shift/hari yang sama | Server | Ditolak |
| 8 | Status hadir/terlambat dari **waktu server** | Server | n/a |

Semua percobaan, berhasil maupun gagal, dicatat di **audit log**.

### 4.2 Skema Database (Ringkas)

```
users           (id, nama, email, password, role[admin/hrd/karyawan], division_id,
                 shift_id, device_id, aktif)
divisions       (id, nama)
shifts          (id, nama, jam_masuk, jam_pulang, toleransi_menit)
locations       (id, nama, lat, lng, radius_m, allowed_ips[json], qr_token, aktif,
                 token_dibuat_pada)
attendances     (id, user_id, location_id, tanggal, tipe[masuk/pulang], waktu_server,
                 lat, lng, akurasi, jarak_m, ip_address, device_id,
                 status[hadir/terlambat/izin/ditolak], catatan)
leave_requests  (id, user_id, jenis[sakit/izin/cuti/terlambat], tgl_mulai, tgl_selesai,
                 jam_estimasi_tiba, alasan, foto_bukti_path, status[menunggu/disetujui/ditolak],
                 approved_by, catatan_admin)
holidays        (id, tanggal, keterangan)
audit_logs      (id, user_id, aksi, hasil, detail[json], ip_address, created_at)
```

### 4.3 Endpoint Utama

```
POST  /login | /logout
POST  /attendance/check-in        (qr_token, lat, lng, accuracy, device_id)
POST  /attendance/check-out
GET   /attendance/history
POST  /leaves                     (jenis, tanggal, alasan, foto_bukti)
GET   /leaves

# Admin
GET    /admin/dashboard
GET    /admin/attendance          (filter tanggal/divisi/status)
GET    /admin/attendance/{id}
PATCH  /admin/leaves/{id}         (setujui/tolak)
POST   /admin/locations/{id}/rotate-token
POST   /admin/users/{id}/reset-device
GET    /admin/reports/export
CRUD   /admin/employees, /divisions, /shifts, /locations, /holidays
```

### 4.4 Pengaman Khusus QR Statis

- Token acak panjang (32+ karakter), bukan ID/koordinat yang mudah ditebak
- QR dicetak dan dipasang **di dalam kantor** (tidak terlihat dari luar)
- Tombol **rotasi** dan **cabut** token di dashboard admin
- Satu token per lokasi/cabang

### 4.5 Keamanan Infrastruktur

- HTTPS wajib + header HSTS
- Password di-hash (bcrypt/argon2), 2FA untuk admin
- Konfigurasi **trusted proxy** dengan benar (kalau memakai Cloudflare/reverse proxy) agar IP tidak bisa dipalsukan lewat `X-Forwarded-For`
- Rate limiting pada login dan endpoint absen
- Upload foto bukti: validasi tipe dan ukuran, simpan di luar folder publik, akses lewat URL bertanda tangan sementara
- Peran dan hak akses: super admin, HRD, atasan divisi
- Backup database terjadwal
- Kebijakan retensi data (misal foto bukti dihapus setelah 12 bulan)

---

## 5. Tahapan Pengerjaan

Estimasi untuk 1-2 developer. Durasi bisa berubah tergantung pengalaman tim dan kelengkapan prasyarat.

### Fase 0 - Persiapan (3-5 hari)
- [ ] Kumpulkan kebutuhan final dan prasyarat (bagian 3)
- [ ] Siapkan server, domain, HTTPS, dan repositori Git
- [ ] Inisialisasi proyek Laravel, konfigurasi environment (dev/staging/production)
- [ ] Susun desain antarmuka (wireframe) halaman utama

**Hasil:** lingkungan siap, kebutuhan terkunci.

### Fase 1 - Fondasi (1 minggu)
- [ ] Migrasi database dan seeder (admin awal, shift, lokasi contoh)
- [ ] Autentikasi, peran (admin/HRD/karyawan), middleware
- [ ] Master data: karyawan, divisi, shift, lokasi (koordinat, radius, IP, token QR)
- [ ] Fitur generate dan cetak QR per lokasi

**Hasil:** admin dapat mengelola data dasar dan mencetak QR.

### Fase 2 - Absensi Inti (1,5-2 minggu)
- [ ] Halaman scan QR (kamera belakang) dan pengambilan GPS di browser
- [ ] Endpoint check-in/check-out dengan seluruh lapisan validasi (bagian 4.1)
- [ ] Perhitungan Haversine, cek IP, penentuan status dengan waktu server
- [ ] Audit log untuk semua percobaan
- [ ] Halaman riwayat absensi karyawan
- [ ] Penanganan error ramah pengguna (izin GPS ditolak, akurasi buruk, dll.)

**Hasil:** karyawan dapat absen dengan aman, MVP inti berjalan.

### Fase 3 - Dashboard Admin (1 minggu)
- [ ] Monitoring kehadiran hari ini (real-time/auto-refresh)
- [ ] Detail absensi dengan peta lokasi dan info validasi
- [ ] Daftar anomali dan percobaan gagal
- [ ] Filter dan pencarian (tanggal, divisi, status)

**Hasil:** admin dapat memantau kehadiran dan anomali.

### Fase 4 - Pengajuan Izin (1-1,5 minggu)
- [ ] Form pengajuan (tidak masuk/terlambat) dengan unggah foto bukti
- [ ] Alur persetujuan admin (setujui/tolak + catatan)
- [ ] Sinkronisasi ke absensi: izin disetujui mengubah status hari itu menjadi izin/sakit/cuti (bukan alpa)
- [ ] Notifikasi status (email/WhatsApp/notifikasi web)

**Hasil:** modul izin lengkap dari karyawan sampai admin.

### Fase 5 - Pengamanan Lanjutan (1 minggu)
- [ ] Device binding dan fitur reset perangkat oleh admin
- [ ] Rotasi/cabut token QR dari dashboard
- [ ] Rate limiting, 2FA admin, trusted proxy, header keamanan
- [ ] Opsional: passkey/WebAuthn untuk memperkuat device binding
- [ ] Pengaturan retensi dan penghapusan data lama

**Hasil:** sistem mencapai target keamanan tinggi.

### Fase 6 - Laporan (4-5 hari)
- [ ] Rekap harian/bulanan per karyawan dan divisi
- [ ] Statistik keterlambatan dan ketidakhadiran
- [ ] Ekspor Excel dan PDF
- [ ] Hari libur dan perhitungan alpa otomatis (job terjadwal)

**Hasil:** laporan siap dipakai HRD.

### Fase 7 - Pengujian dan Peluncuran (1-1,5 minggu)
- [ ] Uji fungsional dan uji penolakan (QR salah, IP luar, GPS jauh, perangkat lain, absen ganda)
- [ ] Uji di berbagai HP dan browser (Android Chrome, iOS Safari)
- [ ] Uji keamanan dasar (OWASP: XSS, CSRF, SQL injection, upload file)
- [ ] Uji beban ringan (banyak karyawan absen bersamaan di jam masuk)
- [ ] **Pilot** dengan 1 divisi (1-2 minggu), kumpulkan umpan balik
- [ ] Pelatihan karyawan dan admin, panduan singkat penggunaan
- [ ] Peluncuran bertahap ke seluruh karyawan

**Hasil:** sistem live di production.

### Perkiraan Total: sekitar 7-9 minggu

| Fase | Perkiraan durasi |
|---|---|
| 0. Persiapan | 3-5 hari |
| 1. Fondasi | 1 minggu |
| 2. Absensi inti | 1,5-2 minggu |
| 3. Dashboard admin | 1 minggu |
| 4. Pengajuan izin | 1-1,5 minggu |
| 5. Pengamanan lanjutan | 1 minggu |
| 6. Laporan | 4-5 hari |
| 7. Pengujian dan peluncuran | 1-1,5 minggu |

---

## 6. Strategi Pengujian (Skenario Kunci)

| Skenario | Hasil yang diharapkan |
|---|---|
| Absen dari kantor, IP dan GPS cocok, perangkat terdaftar | Hadir/Terlambat sesuai waktu server |
| Foto QR dikirim ke orang lain, absen dari luar kantor | Ditolak (IP tidak sesuai) |
| Absen memakai data seluler | Ditolak (IP tidak sesuai) |
| GPS di luar radius | Ditolak |
| Akurasi GPS buruk | Diminta coba ulang |
| Akun dipakai di perangkat lain | Ditolak (perangkat tidak terdaftar) |
| Jam HP diubah | Tidak berpengaruh (waktu server) |
| Absen masuk dua kali | Ditolak |
| QR dicabut/token lama | Ditolak |
| Izin disetujui admin | Status hari itu menjadi izin, bukan alpa |

---

## 7. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| IP kantor berubah/tidak statis | Karyawan sah ditolak | Minta IP statis ke ISP; sediakan daftar IP ganda dan jalur "review manual" darurat oleh admin |
| Fake GPS di browser | Koordinat dipalsukan | Cek IP sebagai penahan utama; pantau pola anomali |
| Titip absen oleh orang yang sedang di kantor (hotspot/pinjam perangkat) | Kehadiran palsu | Device binding + passkey, audit pola, edukasi dan sanksi kebijakan |
| QR statis bocor | Percobaan absen dari luar | Cek IP dan GPS tetap menahan; rotasi/cabut token segera |
| Header `X-Forwarded-For` dipalsukan | Cek IP bisa ditembus | Konfigurasi trusted proxy yang benar |
| Resistensi karyawan | Adopsi rendah | Pilot, pelatihan, UI sederhana |
| Isu privasi data lokasi | Risiko hukum | Consent tertulis, kebijakan retensi, akses terbatas |
| Lonjakan akses jam masuk | Aplikasi lambat | Caching, indeks database, uji beban |

---

## 8. Kriteria Keberhasilan

- [ ] Proses absen selesai dalam waktu kurang dari 10 detik
- [ ] 100% percobaan dari luar IP/radius kantor tertolak pada pengujian
- [ ] Seluruh skenario pengujian di bagian 6 lulus
- [ ] Admin dapat melihat kehadiran real-time dan mengekspor laporan bulanan
- [ ] Pengajuan izin dapat diproses penuh (ajukan, setujui, tercermin di laporan)
- [ ] Pilot berjalan tanpa masalah kritis selama minimal 1 minggu

---

## 9. Pengembangan Lanjutan (Backlog)

1. **QR dinamis** (berganti tiap 30-60 detik): upgrade keamanan paling efektif terhadap risiko foto QR
2. Passkey/WebAuthn wajib untuk verifikasi biometrik perangkat
3. Aplikasi native (deteksi fake GPS dan pembacaan SSID Wi-Fi)
4. Face recognition/liveness
5. Integrasi payroll dan sistem HR
6. Dukungan multi-cabang dengan laporan konsolidasi
7. Pengingat otomatis (belum absen, lupa absen pulang)

---

## 10. Daftar Periksa Sebelum Go-Live

- [ ] HTTPS aktif dan sertifikat valid
- [ ] `APP_DEBUG=false`, kredensial default dihapus
- [ ] IP kantor dan koordinat lokasi sudah diverifikasi di lapangan
- [ ] QR sudah dicetak dan dipasang di dalam kantor
- [ ] Backup otomatis dan prosedur pemulihan diuji
- [ ] 2FA admin aktif, peran dan hak akses sudah diatur
- [ ] Consent karyawan dan kebijakan privasi sudah tersedia
- [ ] Panduan penggunaan dan jalur bantuan (kontak admin/HRD) sudah dibagikan
