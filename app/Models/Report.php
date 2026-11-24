<?php

namespace App\Models;

use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'report_number',
        'branch_id',
        'asset_id',
        'user_id',
        'reporter_name',
        'issue_description',
        'status',
        'technician_id',
        'repair_notes',
        'replaced_parts',
        'repair_cost',
        'reported_at',
        'processed_at',
        'resolved_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReportStatus::class,
            'repair_cost' => 'decimal:2',
            'reported_at' => 'datetime',
            'processed_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /** Akun yang membuat laporan. */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', array_map(
            fn (ReportStatus $status) => $status->value,
            ReportStatus::active(),
        ));
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', ReportStatus::Selesai->value);
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    public function isActive(): bool
    {
        return in_array($this->status, ReportStatus::active(), true);
    }

    /**
     * Daftar suku cadang yang diganti sebagai array.
     *
     * @return list<string>
     */
    public function partsList(): array
    {
        return collect(explode(',', (string) $this->replaced_parts))
            ->map(fn (string $part) => trim($part))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Nomor laporan berikutnya dalam format RPT-{tahun}-{urut 4 digit}.
     */
    public static function nextNumber(): string
    {
        $prefix = 'RPT-'.now()->year.'-';

        $last = static::query()
            ->where('report_number', 'like', $prefix.'%')
            ->orderByDesc('report_number')
            ->value('report_number');

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
