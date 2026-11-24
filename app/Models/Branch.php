<?php

namespace App\Models;

use App\Enums\AssetStatus;
use App\Enums\ReportStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'city',
        'address',
        'phone',
        'manager_name',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ------------------------------------------------------------------
    // Helper & Aggregations
    // ------------------------------------------------------------------

    /**
     * Total biaya servis bulan tertentu (default bulan ini).
     */
    public function monthlyCost(?Carbon $date = null): float
    {
        $date = $date ?? now();

        return (float) $this->reports()
            ->completed()
            ->whereBetween('resolved_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
            ->sum('repair_cost');
    }

    /**
     * Jumlah servis selesai pada bulan tertentu (default bulan ini).
     */
    public function monthlyServicesCount(?Carbon $date = null): int
    {
        $date = $date ?? now();

        return $this->reports()
            ->completed()
            ->whereBetween('resolved_at', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
            ->count();
    }

    /**
     * Total biaya seluruh servis yang pernah tercatat di cabang ini.
     */
    public function totalCost(): float
    {
        return (float) $this->reports()->completed()->sum('repair_cost');
    }

    /**
     * Jumlah kendala aktif saat ini di cabang ini (menunggu / diproses).
     */
    public function activeReportsCount(): int
    {
        return $this->reports()->active()->count();
    }

    /**
     * Scope pencarian cabang
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('manager_name', 'like', "%{$term}%");
        });
    }
}
