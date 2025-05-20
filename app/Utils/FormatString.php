<?php

namespace App\Utils;

use Carbon\Carbon;

class FormatString
{
    /**
     * Formatea una fecha en formato ISO 8601 a Y-m-d H:i:s.
     */
    public static function formatDate(string $date): string
    {
        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new \Exception('La fecha no tiene un formato válido.');
        }
    }

    public static function formatDateWithoutTime(string $date): string
    {
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            throw new \Exception('La fecha no tiene un formato válido.');
        }
    }

    /**
     * Formatea una fecha en formato Y
     */
    public static function formatYear(string $date): string
    {
        try {
            return Carbon::parse($date)->format('Y');
        } catch (\Exception $e) {
            throw new \Exception('La fecha no tiene un formato válido.');
        }
    }

    public static function formatFromDMYToYMD(?string $date): ?string
    {
        try {
            if (is_null($date)) return null;

            return Carbon::createFromFormat('d/m/Y', trim($date))->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new \Exception('La fecha no tiene un formato válido.');
        }
    }
}
