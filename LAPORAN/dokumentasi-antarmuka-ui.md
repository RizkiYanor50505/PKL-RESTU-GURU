# Dokumentasi Desain Antarmuka (User Interface) Mobile-Friendly

Dokumentasi ini merangkum rancangan dan implementasi antarmuka pengguna (UI) untuk **Sistem Absensi Karyawan Berbasis Web (Mobile-First)** sesuai dengan spesifikasi dokumen [rencana-pengerjaan-sistem-absensi.md](file:///c:/NAUFAL/ABSENSI/AbsensiPKL/LAPORAN/rencana-pengerjaan-sistem-absensi.md).

---

## 1. Daftar Halaman & Rute Antarmuka

Aplikasi saat ini aktif berjalan pada development server lokal: `http://127.0.0.1:8000`

| Halaman | Rute Web | Peran Pengguna | Deskripsi Fitur Utama |
|---|---|---|---|
| **Login Pengguna** | [`/login`](http://127.0.0.1:8000/login) | Umum | Masuk akun, notifikasi keterikatan gawai (*device binding*), dan pintasan cepat demo role. |
| **Dashboard Karyawan** | [`/karyawan/dashboard`](http://127.0.0.1:8000/karyawan/dashboard) | Karyawan (Mobile) | Jam digital atomik server real-time (WIB), kartu status absensi hari ini, ringkasan bulanan, dan tombol cepat presensi. |
| **Scan QR & Sensor GPS** | [`/karyawan/scan`](http://127.0.0.1:8000/karyawan/scan) | Karyawan (Mobile) | Tampilan viewfinder kamera belakang dengan laser animasi, status sensor GPS (jarak & akurasi), IP kantor, modal tiket sukses, dan simulasi penolakan. |
| **Pengajuan Izin / Sakit** | [`/karyawan/izin`](http://127.0.0.1:8000/karyawan/izin) | Karyawan (Mobile) | Pilihan kategori izin ramah sentuhan, picker tanggal, jam tiba jika telat, unggah bukti foto dengan preview, dan tab status persetujuan. |
| **Riwayat Absensi** | [`/karyawan/riwayat`](http://127.0.0.1:8000/karyawan/riwayat) | Karyawan (Mobile) | Filter bulan & chip status (Hadir, Telat, Izin), kartu kehadiran harian, dan modal detail jejak verifikasi server. |
| **Dashboard Admin/HRD** | [`/admin/dashboard`](http://127.0.0.1:8000/admin/dashboard) | Admin / HRD | Monitoring kehadiran real-time hari ini, deteksi alert anomali, review & persetujuan izin, manajemen dan rotasi token QR statis, serta reset perangkat. |

---

## 2. Fitur Desain & Prinsip Responsivitas Mobile

1. **Mobile-First & Touch Target (R-03):** Seluruh tombol dan area interaktif dirancang dengan ukuran target sentuh minimal 44 x 44 piksel dengan umpan balik visual (*micro-animation tap-active*).
2. **Fixed Bottom Navigation:** Bilah navigasi bawah modern dengan tombol melayang (*elevated floating center button*) khusus untuk akses instan ke kamera scan QR.
3. **Waktu Server Sebagai Acuan Tunggal:** Jam digital di dashboard menampilkan waktu server terverifikasi, bukan jam perangkat lokal HP.
4. **Indikator Lapisan Keamanan Transparan:** Pada layar scan, pengguna dapat melihat status 8 lapisan validasi (jarak GPS Haversine, akurasi GPS $\le 50$m, IP publik Wi-Fi kantor, dan kecocokan ID perangkat).
5. **Penanganan Error Ramah Pengguna:** Saat validasi gagal (misal berada di luar radius kantor), sistem menampilkan modal penolakan dengan penjelasan yang jelas dan saran solusi tindakan.
