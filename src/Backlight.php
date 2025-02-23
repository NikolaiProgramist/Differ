<?php

namespace Differ\Backlight;

use Exception;

use function cli\line;

/**
 * @throws Exception
 */
function getColors(string $theme): array
{
    $path = realpath(__DIR__ . "/../themes/{$theme}.json");

    try {
        if (!file_exists($path)) {
            throw new Exception("Error load theme: {$theme}");
        }
    } catch (Exception $e) {
        line("Error: theme \"{$theme}\" does not exist");
        exit;
    }

    $content = file_get_contents($path);
    return json_decode($content, true);
}
