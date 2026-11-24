@props(['enum'])

@php
    $resolved = $enum;
    if (is_string($resolved)) {
        $legacyRoleMap = [
            'manajer' => \App\Enums\Role::Admin,
            'karyawan' => \App\Enums\Role::Pegawai,
            'admin' => \App\Enums\Role::Admin,
            'teknisi' => \App\Enums\Role::Teknisi,
            'pegawai' => \App\Enums\Role::Pegawai,
        ];

        $legacyStatusMap = [
            '1' => \App\Enums\UserStatus::Aktif,
            '0' => \App\Enums\UserStatus::Nonaktif,
            'aktif' => \App\Enums\UserStatus::Aktif,
            'nonaktif' => \App\Enums\UserStatus::Nonaktif,
        ];

        $lower = strtolower($resolved);
        $resolved = $legacyRoleMap[$lower]
            ?? $legacyStatusMap[$lower]
            ?? \App\Enums\Role::tryFrom($resolved)
            ?? \App\Enums\UserStatus::tryFrom($resolved)
            ?? \App\Enums\ReportStatus::tryFrom($resolved)
            ?? \App\Enums\AssetStatus::tryFrom($resolved)
            ?? null;
    }
@endphp

@if($resolved && is_object($resolved) && method_exists($resolved, 'tone'))
    <span {{ $attributes->class(['bdg', 'bdg-'.$resolved->tone()]) }}>
        <i class="bi {{ $resolved->icon() }}"></i>{{ $resolved->label() }}
    </span>
@else
    <span {{ $attributes->class(['bdg', 'bdg-secondary']) }}>
        {{ is_object($resolved) && method_exists($resolved, 'label') ? $resolved->label() : ($resolved->value ?? (string) ($enum ?? '—')) }}
    </span>
@endif

