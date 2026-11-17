<?php

namespace App\Models;

use Database\Factories\LokasiFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lokasi extends Model
{
    /** @use HasFactory<LokasiFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_meters',
        'qr_token',
        'allowed_ips',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'radius_meters' => 'integer',
            'allowed_ips' => 'array',
        ];
    }

    /**
     * @return HasMany<Presensi, $this>
     */
    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }
}
