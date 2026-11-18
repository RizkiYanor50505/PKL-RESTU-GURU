<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TitikReklame extends Model
{
    use HasFactory;

    protected $table = 'adv_titik_reklame';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kode_titik',
        'kategori_space',
        'wilayah',
        'alamat_lokasi',
        'latitude',
        'longitude',
        'google_maps_url',
        'ukuran',
        'spesifikasi_lampu',
        'foto_papan',
        'status_titik',
        'no_izin',
        'tgl_izin_berakhir',
        'tgl_pajak_berakhir',
        'keterangan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'tgl_izin_berakhir' => 'date',
            'tgl_pajak_berakhir' => 'date',
        ];
    }

    /**
     * @return HasMany<KontrakSewa, $this>
     */
    public function kontrakSewas(): HasMany
    {
        return $this->hasMany(KontrakSewa::class, 'titik_reklame_id');
    }

    /**
     * @return HasOne<KontrakSewa, $this>
     */
    public function kontrakAktif(): HasOne
    {
        return $this->hasOne(KontrakSewa::class, 'titik_reklame_id')
            ->where('status_kontrak', 'aktif')
            ->latestOfMany('tgl_mulai');
    }
}
