<?php

namespace Database\Factories;

use App\Models\Presensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Presensi>
 */
class PresensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', 'yesterday');
        $masukAt = now(config('attendance.timezone'))
            ->setDateTimeFrom($date)
            ->setTime(7, fake()->numberBetween(30, 59))
            ->utc();

        return [
            'nik' => 'PKL-2026-081',
            'attendance_date' => $masukAt->copy()->timezone(config('attendance.timezone'))->toDateString(),
            'status' => Presensi::STATUS_ON_TIME,
            'late_minutes' => 0,
            'masuk_at' => $masukAt,
            'masuk_latitude' => config('attendance.office_latitude'),
            'masuk_longitude' => config('attendance.office_longitude'),
            'masuk_accuracy' => fake()->randomFloat(1, 3, 30),
            'masuk_distance' => fake()->randomFloat(1, 2, 60),
            'masuk_ip' => fake()->ipv4(),
        ];
    }

    public function late(int $minutes = 24): static
    {
        return $this->state(function (array $attributes) use ($minutes) {
            $masukAt = $attributes['masuk_at']->copy()->timezone(config('attendance.timezone'))->setTime(8, 0)->addMinutes($minutes)->utc();

            return [
                'masuk_at' => $masukAt,
                'status' => Presensi::STATUS_LATE,
                'late_minutes' => $minutes,
            ];
        });
    }

    public function checkedOut(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'pulang_at' => $attributes['masuk_at']->copy()->timezone(config('attendance.timezone'))->setTime(17, 5)->utc(),
                'pulang_latitude' => $attributes['masuk_latitude'],
                'pulang_longitude' => $attributes['masuk_longitude'],
                'pulang_accuracy' => $attributes['masuk_accuracy'],
                'pulang_distance' => $attributes['masuk_distance'],
                'pulang_ip' => $attributes['masuk_ip'],
            ];
        });
    }
}
