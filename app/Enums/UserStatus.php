<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum UserStatus: string
{
    use EnumHelpers;

    case Aktif = 'aktif';
    case Nonaktif = 'nonaktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Nonaktif => 'Non-aktif',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Aktif => 'success',
            self::Nonaktif => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Aktif => 'bi-check-circle',
            self::Nonaktif => 'bi-slash-circle',
        };
    }
}
