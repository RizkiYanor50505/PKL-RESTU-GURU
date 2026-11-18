<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitikReklameSeeder extends Seeder
{
    /**
     * Data dummy meniru pola operasional Restu Guru Promosindo (Internal & Mitra).
     * BUKAN data asli perusahaan agar aman disimpan di repository GitHub tim.
     */
    public function run(): void
    {
        $now = now();

        $dummyTitik = [
            [
                'kode_titik' => 'ADV-DMY-001',
                'kategori_space' => 'internal',
                'wilayah' => 'Banjarbaru',
                'alamat_lokasi' => 'Jl. A. Yani Km. 34 (Patokan: Dekat Bundaran Simpang Empat Banjarbaru)',
                'latitude' => -3.44051200,
                'longitude' => 114.83214500,
                'google_maps_url' => 'https://maps.google.com/?q=-3.440512,114.832145',
                'ukuran' => '4m x 8m (Vertikal)',
                'spesifikasi_lampu' => 'Frontlite LED 4x100W',
                'foto_papan' => null,
                'status_titik' => 'Tersedia',
                'no_izin' => 'IZN-DUMMY-BJB/2026/001',
                'tgl_izin_berakhir' => '2027-06-30',
                'tgl_pajak_berakhir' => '2027-06-30',
                'keterangan' => 'Data dummy: Space Baliho Restu Guru Promosindo menghadap arus arah Banjarmasin.',
            ],
            [
                'kode_titik' => 'ADV-DMY-002',
                'kategori_space' => 'internal',
                'wilayah' => 'Kab. Banjar (Martapura)',
                'alamat_lokasi' => 'Jl. A. Yani Km. 40 Martapura (Patokan: Seberang Area Pertokoan Pusat Kota)',
                'latitude' => null,
                'longitude' => null,
                'google_maps_url' => null,
                'ukuran' => '5m x 10m (Horizontal)',
                'spesifikasi_lampu' => 'Backlite Neon/LED',
                'foto_papan' => null,
                'status_titik' => 'Tersewa',
                'no_izin' => 'IZN-DUMMY-MTP/2026/002',
                'tgl_izin_berakhir' => '2027-03-15',
                'tgl_pajak_berakhir' => '2027-03-15',
                'keterangan' => 'Data dummy: Koordinat belum dilengkapi, baru berupa patokan teks.',
            ],
            [
                'kode_titik' => 'ADV-DMY-003',
                'kategori_space' => 'mitra',
                'wilayah' => 'Kab. Banjar (Gambut)',
                'alamat_lokasi' => 'Jl. A. Yani Km. 14 Gambut (Patokan: Sebelum Persimpangan Jalan Utama)',
                'latitude' => null,
                'longitude' => null,
                'google_maps_url' => null,
                'ukuran' => '4m x 6m (Vertikal)',
                'spesifikasi_lampu' => 'Frontlite 3x100W',
                'foto_papan' => null,
                'status_titik' => 'Tersedia',
                'no_izin' => 'IZN-DUMMY-MTR/2026/003',
                'tgl_izin_berakhir' => '2027-08-20',
                'tgl_pajak_berakhir' => '2027-08-20',
                'keterangan' => 'Data dummy: Space Baliho Mitra Restu Guru Promosindo.',
            ],
            [
                'kode_titik' => 'ADV-DMY-004',
                'kategori_space' => 'internal',
                'wilayah' => 'Banjarmasin',
                'alamat_lokasi' => 'Jl. Jend. Sudirman / Kawasan Pusat Kota Banjarmasin (Patokan: Dekat Jembatan Utama)',
                'latitude' => -3.31943700,
                'longitude' => 114.59075500,
                'google_maps_url' => 'https://maps.google.com/?q=-3.319437,114.590755',
                'ukuran' => '6m x 12m (Bando Jalan)',
                'spesifikasi_lampu' => 'Frontlite LED 6x150W',
                'foto_papan' => null,
                'status_titik' => 'Tersewa',
                'no_izin' => 'IZN-DUMMY-BJM/2026/004',
                'tgl_izin_berakhir' => '2027-01-31',
                'tgl_pajak_berakhir' => '2027-01-31',
                'keterangan' => 'Data dummy: Space Baliho Restu Guru Promosindo jalur padat lalu lintas.',
            ],
            [
                'kode_titik' => 'ADV-DMY-005',
                'kategori_space' => 'mitra',
                'wilayah' => 'Kab. Tapin',
                'alamat_lokasi' => 'Jl. Brigjen H. Hasan Basry Rantau (Patokan: Dekat Area Terminal / Simpang Rantau)',
                'latitude' => null,
                'longitude' => null,
                'google_maps_url' => null,
                'ukuran' => '4m x 8m (Vertikal)',
                'spesifikasi_lampu' => 'Frontlite 4x100W',
                'foto_papan' => null,
                'status_titik' => 'Tersedia',
                'no_izin' => null,
                'tgl_izin_berakhir' => '2027-05-10',
                'tgl_pajak_berakhir' => '2027-05-10',
                'keterangan' => 'Data dummy: Space Baliho Mitra wilayah Tapin.',
            ],
            [
                'kode_titik' => 'ADV-DMY-006',
                'kategori_space' => 'internal',
                'wilayah' => 'Kab. Tanah Laut',
                'alamat_lokasi' => 'Jl. A. Yani Pelaihari (Patokan: Dekat Tugu / Simpang Tiga Masuk Kota)',
                'latitude' => -3.79821000,
                'longitude' => 114.77512000,
                'google_maps_url' => 'https://maps.google.com/?q=-3.798210,114.775120',
                'ukuran' => '4m x 6m (Vertikal)',
                'spesifikasi_lampu' => 'Non-Lampu / Perlu Perbaikan Lampu',
                'foto_papan' => null,
                'status_titik' => 'Maintenance',
                'no_izin' => 'IZN-DUMMY-TLA/2026/006',
                'tgl_izin_berakhir' => '2027-04-01',
                'tgl_pajak_berakhir' => '2027-04-01',
                'keterangan' => 'Data dummy: Sedang pengecekan rangka dan instalasi lampu penerangan.',
            ],
            [
                'kode_titik' => 'ADV-DMY-007',
                'kategori_space' => 'mitra',
                'wilayah' => 'Kab. Tanah Bumbu',
                'alamat_lokasi' => 'Jl. Raya Batulicin (Patokan: Simpang Empat Kawasan Bisnis Batulicin)',
                'latitude' => null,
                'longitude' => null,
                'google_maps_url' => null,
                'ukuran' => '5m x 10m (Horizontal)',
                'spesifikasi_lampu' => 'Backlite LED',
                'foto_papan' => null,
                'status_titik' => 'Tersewa',
                'no_izin' => 'IZN-DUMMY-TBU/2026/007',
                'tgl_izin_berakhir' => '2027-09-15',
                'tgl_pajak_berakhir' => '2027-09-15',
                'keterangan' => 'Data dummy: Space Baliho Mitra wilayah Tanah Bumbu.',
            ],
            [
                'kode_titik' => 'ADV-DMY-008',
                'kategori_space' => 'mitra',
                'wilayah' => 'Kab. Balangan',
                'alamat_lokasi' => 'Jl. A. Yani Paringin (Patokan: Dekat Komplek Perkantoran Paringin)',
                'latitude' => null,
                'longitude' => null,
                'google_maps_url' => null,
                'ukuran' => '4m x 8m (Vertikal)',
                'spesifikasi_lampu' => 'Frontlite 4x100W',
                'foto_papan' => null,
                'status_titik' => 'Tersedia',
                'no_izin' => null,
                'tgl_izin_berakhir' => '2027-10-01',
                'tgl_pajak_berakhir' => '2027-10-01',
                'keterangan' => 'Data dummy: Space Baliho Mitra wilayah Balangan.',
            ],
        ];

        foreach ($dummyTitik as $titik) {
            DB::table('adv_titik_reklame')->updateOrInsert(
                ['kode_titik' => $titik['kode_titik']],
                array_merge($titik, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        // Ambil ID titik yang berstatus 'Tersewa'
        $titikIds = DB::table('adv_titik_reklame')
            ->whereIn('kode_titik', ['ADV-DMY-002', 'ADV-DMY-004', 'ADV-DMY-007'])
            ->pluck('id', 'kode_titik');

        // Data dummy klien (Perusahaan & Perorangan)
        $dummyKlien = [
            [
                'nama_klien' => 'PT Banua Niaga Mandiri (Dummy)',
                'kategori_klien' => 'perusahaan',
                'nama_pic' => 'Hendra Wijaya',
                'no_wa' => '6281200001111',
                'email' => 'hendra@banuaniaga-dummy.test',
                'alamat' => 'Jl. Ahmad Yani Km. 5, Banjarmasin',
                'npwp' => '01.234.567.8-731.000',
            ],
            [
                'nama_klien' => 'CV Borneo Kuliner Nusantara (Dummy)',
                'kategori_klien' => 'perusahaan',
                'nama_pic' => 'Siti Rahmawati',
                'no_wa' => '6281200002222',
                'email' => 'marketing@borneokuliner-dummy.test',
                'alamat' => 'Jl. Panglima Batur, Banjarbaru',
                'npwp' => '02.345.678.9-732.000',
            ],
            [
                'nama_klien' => 'Rahmat Hidayat - Klinik Gigi Senyum (Dummy)',
                'kategori_klien' => 'perorangan',
                'nama_pic' => 'Rahmat Hidayat',
                'no_wa' => '6281300003333',
                'email' => 'rahmat.dummy@example.test',
                'alamat' => 'Jl. Raya Batulicin No. 18, Tanah Bumbu',
                'npwp' => null,
            ],
        ];

        foreach ($dummyKlien as $klien) {
            DB::table('adv_klien')->updateOrInsert(
                ['nama_klien' => $klien['nama_klien']],
                array_merge($klien, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        $klienIds = DB::table('adv_klien')
            ->whereIn('nama_klien', array_column($dummyKlien, 'nama_klien'))
            ->pluck('id', 'nama_klien');

        $userId = DB::table('users')->value('id');

        // Kontrak aktif untuk 3 titik dummy yang berstatus 'Tersewa'
        $dummyKontrak = [
            [
                'titik_reklame_id' => $titikIds['ADV-DMY-002'],
                'klien_id' => $klienIds['CV Borneo Kuliner Nusantara (Dummy)'],
                'user_id' => $userId,
                'no_penawaran' => 'PNW/RGP-DMY/2026/001',
                'no_kontrak' => 'CTR/RGP-DMY/2026/001',
                'tgl_penawaran' => '2026-08-18',
                'tgl_acc' => '2026-08-22',
                'tgl_mulai' => '2026-09-01',
                'tgl_berakhir' => '2026-11-30', // <= 14 hari dari 18 Nov 2026 (uji pengingat perpanjangan)
                'durasi_bulan' => 3,
                'nilai_kontrak' => 18500000.00,
                'materi_iklan' => 'Promo Pembukaan Cabang Martapura & Diskon Paket Keluarga',
                'tgl_pemasangan' => '2026-08-31',
                'file_berita_acara' => null,
                'status_kontrak' => 'aktif',
                'status_perpanjangan' => 'belum_konfirmasi',
                'status_materi' => 'terpasang',
                'catatan' => 'Kontrak dummy perusahaan mendekati jatuh tempo (perlu konfirmasi perpanjangan).',
            ],
            [
                'titik_reklame_id' => $titikIds['ADV-DMY-004'],
                'klien_id' => $klienIds['PT Banua Niaga Mandiri (Dummy)'],
                'user_id' => $userId,
                'no_penawaran' => 'PNW/RGP-DMY/2026/002',
                'no_kontrak' => 'CTR/RGP-DMY/2026/002',
                'tgl_penawaran' => '2026-05-10',
                'tgl_acc' => '2026-05-15',
                'tgl_mulai' => '2026-06-01',
                'tgl_berakhir' => '2027-05-31',
                'durasi_bulan' => 12,
                'nilai_kontrak' => 85000000.00,
                'materi_iklan' => 'Kampanye Produk Tahunan PT Banua Niaga Mandiri',
                'tgl_pemasangan' => '2026-05-30',
                'file_berita_acara' => null,
                'status_kontrak' => 'aktif',
                'status_perpanjangan' => 'belum_konfirmasi',
                'status_materi' => 'terpasang',
                'catatan' => 'Kontrak dummy perusahaan 1 tahun penuh.',
            ],
            [
                'titik_reklame_id' => $titikIds['ADV-DMY-007'],
                'klien_id' => $klienIds['Rahmat Hidayat - Klinik Gigi Senyum (Dummy)'],
                'user_id' => $userId,
                'no_penawaran' => 'PNW/RGP-DMY/2026/003',
                'no_kontrak' => 'CTR/RGP-DMY/2026/003',
                'tgl_penawaran' => '2026-09-20',
                'tgl_acc' => '2026-09-23',
                'tgl_mulai' => '2026-10-01',
                'tgl_berakhir' => '2027-03-31',
                'durasi_bulan' => 6,
                'nilai_kontrak' => 24000000.00,
                'materi_iklan' => 'Layanan Kesehatan Gigi & Pemasangan Behel Klinik Senyum',
                'tgl_pemasangan' => '2026-09-30',
                'file_berita_acara' => null,
                'status_kontrak' => 'aktif',
                'status_perpanjangan' => 'belum_konfirmasi',
                'status_materi' => 'terpasang',
                'catatan' => 'Kontrak dummy perorangan (bayar di awal sebelum produksi & pasang).',
            ],
        ];

        foreach ($dummyKontrak as $kontrak) {
            DB::table('adv_kontrak_sewa')->updateOrInsert(
                ['no_penawaran' => $kontrak['no_penawaran']],
                array_merge($kontrak, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }

        $kontrakPeroranganId = DB::table('adv_kontrak_sewa')
            ->where('no_penawaran', 'PNW/RGP-DMY/2026/003')
            ->value('id');

        if ($kontrakPeroranganId) {
            DB::table('adv_pembayaran')->updateOrInsert(
                [
                    'kontrak_sewa_id' => $kontrakPeroranganId,
                    'tgl_bayar' => '2026-09-24',
                ],
                [
                    'user_id' => $userId,
                    'jumlah_bayar' => 24000000.00,
                    'jenis_pembayaran' => 'bayar_awal',
                    'metode_pembayaran' => 'Transfer Bank',
                    'bukti_pembayaran' => null,
                    'catatan' => 'Pembayaran awal lunas klien perorangan sebelum produksi & pasang (dummy).',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
