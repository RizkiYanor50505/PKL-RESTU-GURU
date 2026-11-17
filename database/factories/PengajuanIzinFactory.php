<?php

namespace Database\Factories;

use App\Models\PengajuanIzin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PengajuanIzin>
 */
class PengajuanIzinFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'jenis' => fake()->randomElement(PengajuanIzin::TYPES),
            'tgl_mulai' => now()->toDateString(),
            'tgl_selesai' => now()->addDays(1)->toDateString(),
            'alasan' => fake()->sentence(),
            'foto_bukti' => null,
            'status_izin' => 'menunggu',
            'approved_by' => null,
            'catatan_admin' => null,
        ];
    }

    public function approved(?User $approver = null): static
    {
        return $this->state(fn () => [
            'status_izin' => 'disetujui',
            'approved_by' => $approver?->id ?? User::factory()->state(['role' => 'manajer']),
            'catatan_admin' => 'Disetujui untuk permohonan resmi.',
        ]);
    }
}
