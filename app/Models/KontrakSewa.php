<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KontrakSewa extends Model
{
    use HasFactory;

    protected $table = 'adv_kontrak_sewa';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'titik_reklame_id',
        'klien_id',
        'user_id',
        'no_penawaran',
        'no_kontrak',
        'tgl_penawaran',
        'tgl_acc',
        'tgl_mulai',
        'tgl_berakhir',
        'durasi_bulan',
        'nilai_kontrak',
        'materi_iklan',
        'tgl_pemasangan',
        'file_berita_acara',
        'status_kontrak',
        'status_perpanjangan',
        'status_materi',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tgl_penawaran' => 'date',
            'tgl_acc' => 'date',
            'tgl_mulai' => 'date',
            'tgl_berakhir' => 'date',
            'tgl_pemasangan' => 'date',
            'durasi_bulan' => 'integer',
            'nilai_kontrak' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<TitikReklame, $this>
     */
    public function titikReklame(): BelongsTo
    {
        return $this->belongsTo(TitikReklame::class, 'titik_reklame_id');
    }

    /**
     * @return BelongsTo<Klien, $this>
     */
    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class, 'klien_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<Pembayaran, $this>
     */
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'kontrak_sewa_id');
    }
}
