<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format a money value, removing decimal zeros if it's a whole number.
     * Example: 16440.00 -> 16,440
     * Example: 16440.50 -> 16,440.50
     */
    public static function money($value)
    {
        if (empty($value)) return '0';
        $val = floatval($value);
        return number_format($val, fmod($val, 1) !== 0.0 ? 2 : 0);
    }
}
