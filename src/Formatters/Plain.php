<?php

namespace Differ\Formatters\Plain;

use Exception;

use function Differ\Backlight\getColors;

const ADD_MARKER = 'added';
const REMOVE_MARKER = 'removed';
const UPDATED_MARKER = 'updated';

/**
 * @throws Exception
 */
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
                    // phpcs:ignore
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['end']}m\033[{$colors['text']}m was \033[{$colors['add']}m{$addMarker}\033[{$colors['text']}m with value: {$value}\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    // phpcs:ignore
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['text']}m was \033[{$colors['remove']}m{$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }

                $string = plain($keyData['children'], $theme, $newPath, $depth + 1);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = getString($keyData['value'], $theme);

                if ($status === 'add') {
                    // phpcs:ignore
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['text']}m was \033[{$colors['add']}m{$addMarker}\033[{$colors['text']}m with value: {$value}\n";
                    return "{$resultString}{$string}";
                }

                if ($status === 'remove') {
                    // phpcs:ignore
                    $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['text']}m was \033[{$colors['remove']}m{$removeMarker}\033[{$colors['end']}m\n";
                    return "{$resultString}{$string}";
                }
            }

            $beforeValue = getString($keyData['beforeValue'], $theme);
            $afterValue = getString($keyData['afterValue'], $theme);

            // phpcs:ignore
            $string = "\033[{$colors['text']}mProperty \033[{$colors['primary']}m'{$newPath}'\033[{$colors['text']}m was {$updatedMarker}. From {$beforeValue}\033[{$colors['text']}m to {$afterValue}\n";
            return "{$resultString}{$string}";
        },
        ''
    );
}

/**
 * @throws Exception
 */
function getString(mixed $string, $theme): string
{
    $colors = getColors($theme);

    if (is_bool($string)) {
        // phpcs:ignore
        return $string ? "\033[{$colors['special']}mtrue\033[{$colors['end']}m" : "\033[{$colors['special']}mfalse\033[{$colors['end']}m";
    }

    if (is_null($string)) {
        return "\033[{$colors['special']}mnull\033[{$colors['end']}m";
    }

    if (is_array($string)) {
        return "\033[{$colors['complex']}m[complex value]\033[{$colors['end']}m";
    }

    if (is_numeric($string)) {
        return "\033[{$colors['number']}m{$string}\033[{$colors['end']}m";
    }

    if (is_string($string)) {
        return "\033[{$colors['string']}m'{$string}'\033[{$colors['end']}m";
    }

    return $string;
}
