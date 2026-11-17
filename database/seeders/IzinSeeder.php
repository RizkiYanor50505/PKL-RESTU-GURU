<?php

namespace Database\Seeders;

use App\Models\PengajuanIzin;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class IzinSeeder extends Seeder
{
    /**
     * Seed a sample employee with one request waiting for manager approval.
     */
    public function run(): void
    {
        $siti = User::firstOrCreate(
            ['email' => 'siti.nurhaliza@perusahaan.co.id'],
            [
                'name' => 'Siti Nurhaliza',
                'nik' => 'PKL-2026-052',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'is_active' => true,
                'division' => 'Keuangan',
                'shift_id' => Shift::query()->value('id'),
            ]
        );

        PengajuanIzin::firstOrCreate(
            ['user_id' => $siti->id, 'jenis' => 'Sakit', 'tgl_mulai' => now()->toDateString()],
            [
                'tgl_selesai' => now()->addDay()->toDateString(),
                'alasan' => 'Izin sakit asam lambung akut, surat keterangan istirahat dari dokter klinik terlampir.',
                'status_izin' => PengajuanIzin::STATUS_PENDING,
            ]
        );
    }
}
