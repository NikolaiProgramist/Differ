<?php

namespace Differ\Formatters;

use function Differ\Formatters\Plain\plain;
use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Json\json;

function selectFormatter(array $tree, string $format, string $theme): string
{
    return match ($format) {
        'plain' => plain($tree, $theme),
        'json' => json($tree),
        default => stylish($tree),
    };
}
