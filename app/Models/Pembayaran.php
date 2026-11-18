<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'adv_pembayaran';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'kontrak_sewa_id',
        'user_id',
        'tgl_bayar',
        'jumlah_bayar',
        'jenis_pembayaran',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tgl_bayar' => 'date',
            'jumlah_bayar' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<KontrakSewa, $this>
     */
    public function kontrakSewa(): BelongsTo
    {
        return $this->belongsTo(KontrakSewa::class, 'kontrak_sewa_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
