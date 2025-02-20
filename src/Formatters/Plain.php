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
                $value = getString($keyData['children'], $theme);

                if ($status === 'add') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['end']}m \033[{$colors['text']}mwas {$addMarker} with value: \033[{$colors['end']}m{$value}\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas \033[{$colors['symbol']}m{$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }

                $string = plain($keyData['children'], $theme, $newPath, $depth + 1);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = getString($keyData['value'], $theme);

                if ($status === 'add') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas {$addMarker} with value: {$value}\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas \033[{$colors['symbol']}m{$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }
            }

            $beforeValue = getString($keyData['beforeValue'], $theme);
            $afterValue = getString($keyData['afterValue'], $theme);

            $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}' \033[{$colors['text']}mwas {$updatedMarker}. From {$beforeValue} to {$afterValue}\n";
            return "{$resultString}{$string}";
        },
        ''
    );
}

function getString(mixed $string, $theme): string
{
    $colors = getColors($theme);

    if (is_bool($string)) {
        return $string ? "\033[{$colors['special']}mtrue\033[{$colors['end']}m" : "\033[{$colors['special']}mfalse\033[{$colors['end']}m";
    }

    if (is_null($string)) {
        return "\033[{$colors['special']}mnull\033[{$colors['end']}m";
    }

    if (is_array($string)) {
        return "\033[{$colors['complex']}m[complex value]\033[{$colors['end']}m";
    }

    if (is_numeric($string)) {
        return "\033[{$colors['symbol']}m{$string}\033[{$colors['end']}m";
    }

    if (is_string($string)) {
        return "\033[{$colors['symbol']}m'{$string}'\033[{$colors['end']}m";
    }

    return $string;
}
