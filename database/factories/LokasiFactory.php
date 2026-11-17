<?php

namespace Database\Factories;

use App\Models\Lokasi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lokasi>
 */
class LokasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_lokasi' => 'Kantor Pusat Jakarta',
            'latitude' => config('attendance.office_latitude', -6.1753924),
            'longitude' => config('attendance.office_longitude', 106.8271528),
            'radius_meters' => (int) config('attendance.radius_meters', 100),
            'qr_token' => config('attendance.qr_token', 'LOC-PST-99A'),
            'allowed_ips' => ['103.14.88.19', '127.0.0.1'],
        ];
    }
}
