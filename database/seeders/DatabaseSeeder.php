<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $shiftPagi = Shift::firstOrCreate(
            ['nama_shift' => 'Shift Pagi'],
            [
                'jam_masuk' => '08:00:00',
                'jam_pulang' => '17:00:00',
                'toleransi_menit' => 15,
            ]
        );

        Lokasi::firstOrCreate(
            ['qr_token' => config('attendance.qr_token', 'LOC-PST-99A')],
            [
                'nama_lokasi' => 'Kantor Pusat Jakarta',
                'latitude' => config('attendance.office_latitude', -6.1753924),
                'longitude' => config('attendance.office_longitude', 106.8271528),
                'radius_meters' => (int) config('attendance.radius_meters', 100),
                'allowed_ips' => ['103.14.88.19', '127.0.0.1'],
            ]
        );

        User::firstOrCreate(
            ['email' => 'ahmad.fauzi@perusahaan.co.id'],
            [
                'name' => 'Ahmad Fauzi',
                'nik' => 'PKL-2026-081',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'device_id' => 'dev-a54-98a3',
                'is_active' => true,
                'division' => 'Divisi IT Development',
                'shift_id' => $shiftPagi->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'manajer.hrd@perusahaan.co.id'],
            [
                'name' => 'Budi Santoso',
                'nik' => 'MGR-2026-014',
                'password' => Hash::make('password'),
                'role' => 'manajer',
                'device_id' => 'dev-mgr-014',
                'is_active' => true,
                'division' => 'Manajer Operasional & HRD',
                'shift_id' => $shiftPagi->id,
            ]
        );

        $this->call(IzinSeeder::class);
    }
}
