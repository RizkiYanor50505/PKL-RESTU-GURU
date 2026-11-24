<?php

namespace App\Enums\Concerns;

trait EnumHelpers
{
    /**
     * Seluruh nilai backing enum, berguna untuk aturan validasi `Rule::in()`.
     *
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Pasangan value => label untuk dropdown/filter.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
