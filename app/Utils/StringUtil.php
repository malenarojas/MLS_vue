<?php

namespace App\Utils;

class StringUtil
{
    public static function addFolderToFilePath(string $path, string $folder): string
    {
        $fileName = basename($path);
        $currentFolder = dirname($path);
        $newPath = $currentFolder . '/' . $folder . '/' . $fileName;
        return $newPath;
    }

    public static function getFirstPart(string $string): ?string
    {
        $parts = explode('-', $string);

        return $parts[0] ?? null;
    }

    public static function getOfficeIdfromAgentId(string $agentId)
    {
        return substr($agentId, 0, 6);
    }
}
