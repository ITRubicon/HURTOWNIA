<?php

namespace App\Utilities;

class DateValidator
{
    public static function isDbDateFormat(string $date): bool
    {
        return preg_match('/\d{4}-\d{2}-\d{2}/', $date);
    }

    public static function isDateCorrect(string $date): bool
    {
        $from = date('Y-m-d', strtotime($date));
        return $from === $date;
    }
}