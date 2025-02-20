<?php

namespace Differ\Backlight;

use Exception;

/**
 * @throws Exception
 */
function getColors(string $theme): array
{
    $path = realpath(__DIR__ . "/../themes/{$theme}.json");

    if (!file_exists($path)) {
        throw new Exception("Error load theme: {$theme}");
    }

    $content = file_get_contents($path);
    return json_decode($content, true);
}
