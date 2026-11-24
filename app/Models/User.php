<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nik',
        'email',
        'password',
        'role',
        'device_id',
        'is_active',
        'division',
        'shift_id',
        'branch_id',
        'status',
        'jabatan',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Shift, $this>
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * @return HasMany<Presensi, $this>
     */
    public function presensis(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    /**
     * @return HasMany<PengajuanIzin, $this>
     */
    public function pengajuanIzins(): HasMany
    {
        return $this->hasMany(PengajuanIzin::class);
    }

    public function isManager(): bool
    {
        return $this->role === 'manajer' || $this->role === 'hrd' || $this->role === 'admin' || session('auth_role') === 'manajer';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'manajer' || $this->role === 'admin' || session('auth_role') === 'manajer';
    }

    public function isTechnician(): bool
    {
        return $this->role === 'teknisi' || str_contains(strtolower((string) $this->division), 'teknisi');
    }

    public function canHandleReports(): bool
    {
        return $this->role === 'manajer' || $this->role === 'admin' || $this->role === 'teknisi' || session('auth_role') === 'manajer';
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function getStatusAttribute(): \App\Enums\UserStatus
    {
        return $this->is_active ? \App\Enums\UserStatus::Aktif : \App\Enums\UserStatus::Nonaktif;
    }

    public function setStatusAttribute(\App\Enums\UserStatus|string|null $value): void
    {
        $val = $value instanceof \App\Enums\UserStatus ? $value->value : (string) $value;
        $this->attributes['is_active'] = ($val === \App\Enums\UserStatus::Aktif->value || $val === '1' || $val === 'true' || $val === true);
    }

    public function getJabatanAttribute(): ?string
    {
        return $this->attributes['jabatan'] ?? $this->division ?? null;
    }

    public function setJabatanAttribute(?string $value): void
    {
        $this->attributes['division'] = $value;
    }

    public function initials(): string
    {
        return collect(explode(' ', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn (string $w) => strtoupper(substr($w, 0, 1)))
            ->implode('');
    }

    /**
     * @return BelongsTo<Branch, $this>
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
