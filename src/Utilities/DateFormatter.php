<?php

namespace App\Utilities;

use DateTime;

class DateFormatter
{
    public static function formatDate(string $date, string $format = 'Y-m-d')
    {
        if (empty($date))
            return null;
            
        $dt = new DateTime($date);
        return $dt->format($format);
    }

    public static function formatJson($data)
    {
        return json_encode($data);
    }

    public static function formatInt($data)
    {
        return intval($data);
    }

    public static function formatFloat($data)
    {
        return floatval($data);
    }
}