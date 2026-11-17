<?php

namespace App\Models;

use Database\Factories\PengajuanIzinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanIzin extends Model
{
    /** @use HasFactory<PengajuanIzinFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'menunggu';

    public const STATUS_APPROVED = 'disetujui';

    public const STATUS_REJECTED = 'ditolak';

    public const TYPES = ['Sakit', 'Izin Khusus', 'Cuti Tahunan', 'Izin Terlambat'];

    public const ANNUAL_LEAVE_QUOTA = 12;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'alasan',
        'foto_bukti',
        'status_izin',
        'approved_by',
        'catatan_admin',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tgl_mulai' => 'date',
            'tgl_selesai' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPending(): bool
    {
        return $this->status_izin === self::STATUS_PENDING;
    }

    /**
     * Inclusive number of calendar days covered by the request.
     */
    public function durationDays(): int
    {
        return $this->tgl_mulai->diffInDays($this->tgl_selesai) + 1;
    }
}
