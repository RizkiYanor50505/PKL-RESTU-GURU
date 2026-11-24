<?php

namespace Database\Seeders;

use App\Enums\ReportStatus;
use App\Models\Asset;
use App\Models\Branch;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;

class PemeliharaanAlatSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Cabang Restu Guru Promosindo
        $bjb = Branch::firstOrCreate(
            ['code' => 'BJB'],
            [
                'name' => 'Cabang Banjarbaru',
                'city' => 'Banjarbaru',
                'address' => 'Jl. A. Yani Km. 34 No. 12, Loktabat Utara',
                'phone' => '(0511) 4772890',
                'manager_name' => 'Rahmat Hidayat',
            ]
        );

        $bjm = Branch::firstOrCreate(
            ['code' => 'BJM'],
            [
                'name' => 'Cabang Banjarmasin',
                'city' => 'Banjarmasin',
                'address' => 'Jl. Brigjend H. Hasan Basry No. 88, Kayu Tangi',
                'phone' => '(0511) 3305612',
                'manager_name' => 'Muhammad Firdaus',
            ]
        );

        $mtp = Branch::firstOrCreate(
            ['code' => 'MTP'],
            [
                'name' => 'Cabang Martapura',
                'city' => 'Martapura',
                'address' => 'Jl. A. Yani No. 45, Cindai Alus',
                'phone' => '(0511) 4721995',
                'manager_name' => 'Akhmad Zaini',
            ]
        );

        $lag = Branch::firstOrCreate(
            ['code' => 'LAG'],
            [
                'name' => 'Cabang Liang Anggang',
                'city' => 'Banjarbaru',
                'address' => 'Jl. Jurusan Pelaihari Km. 20, Liang Anggang',
                'phone' => '(0511) 4705123',
                'manager_name' => 'Hendra Saputra',
            ]
        );

        $plh = Branch::firstOrCreate(
            ['code' => 'PLH'],
            [
                'name' => 'Cabang Pelaihari',
                'city' => 'Pelaihari',
                'address' => 'Jl. Kemakmuran No. 15, Pelaihari, Tanah Laut',
                'phone' => '(0512) 21456',
                'manager_name' => 'Dedi Kurniawan',
            ]
        );

        // 2. Mesin & Aset Contoh
        $asset1 = Asset::firstOrCreate(
            ['code' => 'AST-DP-001'],
            [
                'branch_id' => $bjb->id,
                'name' => 'Mesin Digital Printing Outdoor 01',
                'subtitle' => 'Mesin Cetak Banner & Baliho Skala Besar',
                'location' => 'Workshop Cetak Outdoor',
                'brand' => 'Roland Soljet Pro 4 XR-640',
                'status' => 'normal',
                'description' => 'Mesin utama produksi spanduk dan baliho reklame.',
            ]
        );

        $asset2 = Asset::firstOrCreate(
            ['code' => 'AST-LC-002'],
            [
                'branch_id' => $bjb->id,
                'name' => 'Mesin Laser Cutting Acrylic 01',
                'subtitle' => 'Pemotong Presisi Huruf Timbul & Neon Box',
                'location' => 'Ruang Akrilik & Finishing',
                'brand' => 'Trotec Speedy 400',
                'status' => 'normal',
                'description' => 'Digunakan untuk pemotongan akrilik dan pembuatan display reklame.',
            ]
        );

        $asset3 = Asset::firstOrCreate(
            ['code' => 'AST-OF-003'],
            [
                'branch_id' => $bjm->id,
                'name' => 'Mesin Offset Printing 01',
                'subtitle' => 'Cetak Brosur & Kalender Komersial',
                'location' => 'Lantai 1 Workshop',
                'brand' => 'Heidelberg Speedmaster SM 52',
                'status' => 'normal',
                'description' => 'Unit mesin cetak brosur, flyer, dan materi promosi indoor.',
            ]
        );

        $asset4 = Asset::firstOrCreate(
            ['code' => 'AST-PS-004'],
            [
                'branch_id' => $lag->id,
                'name' => 'Mesin Plotter & Cutting Sticker 01',
                'subtitle' => 'Pemotong Sticker & Polyflex Presisi',
                'location' => 'Workshop Finishing',
                'brand' => 'Roland CAMM-1 GS-24',
                'status' => 'normal',
                'description' => 'Mesin pemotong stiker untuk reklame, etalase, dan branding mobil.',
            ]
        );

        $asset5 = Asset::firstOrCreate(
            ['code' => 'AST-DI-005'],
            [
                'branch_id' => $plh->id,
                'name' => 'Mesin Digital Printing Indoor 01',
                'subtitle' => 'Cetak Poster, X-Banner & Foto Resolusi Tinggi',
                'location' => 'Ruang Cetak Indoor',
                'brand' => 'Epson SureColor SC-S80670',
                'status' => 'normal',
                'description' => 'Cetak materi indoor beresolusi tinggi dengan tinta eco-solvent.',
            ]
        );

        $user = User::first();

        // 3. Laporan Contoh
        Report::firstOrCreate(
            ['report_number' => 'RPT-'.date('Y').'-0001'],
            [
                'branch_id' => $bjb->id,
                'asset_id' => $asset1->id,
                'user_id' => $user?->id,
                'reporter_name' => 'Ahmad Fauzi',
                'issue_description' => 'Hasil cetak terdapat garis horizontal tipis pada warna magenta.',
                'status' => ReportStatus::Menunggu,
                'reported_at' => now()->subDays(1),
            ]
        );
    }
}
