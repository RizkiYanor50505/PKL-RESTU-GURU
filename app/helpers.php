<?php

if (! function_exists('rupiah')) {
    /**
     * Format angka menjadi format mata uang Rupiah.
     */
    function rupiah(?float $amount, string $prefix = 'Rp '): string
    {
        if ($amount === null) {
            return '—';
        }

        return $prefix . number_format($amount, 0, ',', '.');
    }
}
