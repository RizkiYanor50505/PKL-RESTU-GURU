<?php

namespace App\Models;

use App\Enums\AssetStatus;
use App\Enums\ReportStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'subtitle',
        'location',
        'brand',
        'status',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AssetStatus::class,
        ];
    }

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /** Laporan yang belum tuntas (menunggu / diproses). */
    public function activeReports(): HasMany
    {
        return $this->reports()->whereIn('status', array_map(
            fn (ReportStatus $status) => $status->value,
            ReportStatus::active(),
        ));
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('brand', 'like', "%{$term}%")
                ->orWhere('subtitle', 'like', "%{$term}%");
        });
    }

    // ------------------------------------------------------------------
    // Domain logic
    // ------------------------------------------------------------------

    /**
     * Sinkronisasi status aset berdasarkan laporan kendala yang aktif:
     *  - ada laporan "diproses"  => dalam_perbaikan
     *  - ada laporan "menunggu"  => dalam_kendala
     *  - tidak ada laporan aktif => normal
     */
    public function syncStatus(): self
    {
        $activeStatuses = $this->activeReports()->pluck('status')->map(
            fn (ReportStatus|string $status) => $status instanceof ReportStatus ? $status : ReportStatus::from($status)
        );

        $status = match (true) {
            $activeStatuses->contains(ReportStatus::Diproses) => AssetStatus::DalamPerbaikan,
            $activeStatuses->contains(ReportStatus::Menunggu) => AssetStatus::DalamKendala,
            default => AssetStatus::Normal,
        };

        if ($this->status !== $status) {
            $this->forceFill(['status' => $status])->save();
        }

        return $this;
    }
}
