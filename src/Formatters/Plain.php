<?php

namespace Differ\Formatters\Plain;

use function Differ\Backlight\getColors;

const ADD_MARKER = 'added';
const REMOVE_MARKER = 'removed';
const UPDATED_MARKER = 'updated';

function plain(array $tree, string $theme, string $path = '', int $depth = 1): string
{
    return array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $path, $depth, $theme) {
            $keyData = $tree[$key];
            $status = $tree[$key]['status'] ?? 'add';
            $newPath = $depth === 1 ? $key : "{$path}.{$key}";
            $resultString = $acc;

            $addMarker = ADD_MARKER;
            $removeMarker = REMOVE_MARKER;
            $updatedMarker = UPDATED_MARKER;

            $colors = getColors($theme);

            if ($status === 'unchanged') {
                return $resultString;
            }

            if (array_key_exists('children', $keyData)) {
                $value = getString($keyData['children']);

                if ($status === 'add') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['end']}m \033[{$colors['text']}mwas {$addMarker} with value: \033[{$colors['symbols']}m{$value}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas {$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }

                $string = plain($keyData['children'], $theme, $newPath, $depth + 1);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = getString($keyData['value']);

                if ($status === 'add') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas {$addMarker} with value: \033[{$colors['symbols']}m{$value}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas \033[{$colors['symbols']}m{$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }
            }

            $beforeValue = getString($keyData['beforeValue']);
            $afterValue = getString($keyData['afterValue']);

            $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas {$updatedMarker}. From \033[{$colors['symbols']}m{$beforeValue} \033[{$colors['text']}mto \033[{$colors['symbols']}m{$afterValue}\033[{$colors['end']}m\n";
            return "{$resultString}{$string}";
        },
        ''
    );
}

function getString(mixed $string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    if (is_array($string)) {
        return '[complex value]';
    }

    if (is_string($string)) {
        return "'{$string}'";
    }

    return $string;
}
