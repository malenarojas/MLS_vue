<?php

namespace App\Utils;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StringGenerateKey
{
    public static function generateKey(): string
    {
        Log::info("Se esta generando una nueva key");
        return Str::uuid()->toString();
    }
    // public static function generateKey(): string
    // {
    //     return strtoupper(uniqid());
    // }
    public static function generateMLSID(
        string $regionId,
        string $officeId,
        string $agentId,
        string $number
    ): string {
        // Tomamos los primeros 4 caracteres
        $regionId = self::padNumber(substr($regionId, 0, 3));
        $officeId = self::padNumber(substr($officeId, 0, 3));
        $agentId = self::padNumber(substr($agentId, 0, 3));

        return Str::slug("{$regionId}{$officeId}{$agentId}-{$number}");
    }
    public static function generateAgentInternal(

        string $officeId,
        string $number
    ): string {

        $officeId = self::padNumber(substr($officeId, 0, 6));
        $number = self::padNumber($number, 3);
        return Str::slug("{$officeId}{$number}");
    }

    public static function padNumber(string $number, int $length = 3): string
    {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }
}
