<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum Role: string
{
    use EnumHelpers;

    case Admin = 'admin';
    case Teknisi = 'teknisi';
    case Pegawai = 'pegawai';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Teknisi => 'Teknisi',
            self::Pegawai => 'Pegawai / Operator',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Admin => 'primary',
            self::Teknisi => 'info',
            self::Pegawai => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Admin => 'bi-shield-check',
            self::Teknisi => 'bi-wrench-adjustable',
            self::Pegawai => 'bi-person-workspace',
        };
    }
}
