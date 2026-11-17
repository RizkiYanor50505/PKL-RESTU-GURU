<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\PresensiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    /** @use HasFactory<PresensiFactory> */
    use HasFactory;

    public const STATUS_ON_TIME = 'tepat_waktu';

    public const STATUS_LATE = 'terlambat';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'lokasi_id',
        'nik',
        'attendance_date',
        'status',
        'late_minutes',
        'masuk_at',
        'masuk_latitude',
        'masuk_longitude',
        'masuk_accuracy',
        'masuk_distance',
        'masuk_ip',
        'pulang_at',
        'pulang_latitude',
        'pulang_longitude',
        'pulang_accuracy',
        'pulang_distance',
        'pulang_ip',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'masuk_at' => 'datetime',
            'pulang_at' => 'datetime',
            'masuk_accuracy' => 'float',
            'masuk_distance' => 'float',
            'pulang_accuracy' => 'float',
            'pulang_distance' => 'float',
            'late_minutes' => 'integer',
        ];
    }

    public function isLate(): bool
    {
        return $this->status === self::STATUS_LATE;
    }

    public function masukLocal(): ?CarbonInterface
    {
        return $this->masuk_at?->copy()->timezone(config('attendance.timezone'));
    }

    public function pulangLocal(): ?CarbonInterface
    {
        return $this->pulang_at?->copy()->timezone(config('attendance.timezone'));
    }

    /**
     * Distance of the most recent scan of the day (checkout if present, otherwise check-in).
     */
    public function latestDistance(): ?float
    {
        return $this->pulang_distance ?? $this->masuk_distance;
    }

    /**
     * Human readable duration between check-in and check-out, e.g. "9 Jam 16 Menit".
     */
    public function workDurationLabel(): ?string
    {
        if ($this->masuk_at === null || $this->pulang_at === null) {
            return null;
        }

        $minutes = (int) $this->masuk_at->diffInMinutes($this->pulang_at);

        return intdiv($minutes, 60).' Jam '.($minutes % 60).' Menit';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Lokasi, $this>
     */
    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }
}
