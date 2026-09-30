<?php

namespace Differ\Parser;

use Exception;
use Symfony\Component\Yaml\Yaml;

use function Differ\Formatters\selectFormatter;

/**
 * @throws Exception
 */
function parse(array $resultDiff, string $format, string $theme): string
{
    return selectFormatter($resultDiff, $format, $theme);
}

/**
 * @throws Exception
 */
function parseToJson(string $path): array
{
    $content = getFileContent($path);
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $message = "The file format is not supported by the current version of the program: {$extension}";

    return match ($extension) {
        'json' => json_decode($content, true),
        'yaml' => Yaml::parse($content),
        default => throw new Exception($message)
    };
}

/**
 * @throws Exception
 */
function getFileContent(string $path): string
{
    $currentPath = realpath($path);

    if ($currentPath === false) {
        throw new Exception("File does not exist: {$path}");
    }

    return file_get_contents($currentPath);
}
