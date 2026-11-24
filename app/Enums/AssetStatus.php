<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum AssetStatus: string
{
    use EnumHelpers;

    case Normal = 'normal';
    case DalamPerbaikan = 'dalam_perbaikan';
    case DalamKendala = 'dalam_kendala';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Beroperasi Normal',
            self::DalamPerbaikan => 'Dalam Perbaikan',
            self::DalamKendala => 'Ada Kendala',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Normal => 'success',
            self::DalamPerbaikan => 'info',
            self::DalamKendala => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Normal => 'bi-check-circle',
            self::DalamPerbaikan => 'bi-tools',
            self::DalamKendala => 'bi-exclamation-triangle',
        };
    }
}
