<?php

namespace App\Enums;

use App\Enums\Concerns\EnumHelpers;

enum ReportStatus: string
{
    use EnumHelpers;

    case Menunggu = 'menunggu';
    case Diproses = 'diproses';
    case Selesai = 'selesai';
    case Dibatalkan = 'dibatalkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu',
            self::Diproses => 'Diproses',
            self::Selesai => 'Selesai',
            self::Dibatalkan => 'Dibatalkan',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Diproses => 'info',
            self::Selesai => 'success',
            self::Dibatalkan => 'secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Menunggu => 'bi-hourglass-split',
            self::Diproses => 'bi-gear-wide-connected',
            self::Selesai => 'bi-check2-circle',
            self::Dibatalkan => 'bi-x-circle',
        };
    }

    /**
     * Status yang masih membuat mesin terhitung bermasalah.
     *
     * @return list<self>
     */
    public static function active(): array
    {
        return [self::Menunggu, self::Diproses];
    }
}
