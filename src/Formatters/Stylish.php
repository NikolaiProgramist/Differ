<?php

namespace Differ\Formatters\Stylish;

use Exception;

use function Differ\Backlight\getColors;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

/**
 * @throws Exception
 */
function stylish(array $tree, $theme, string $replacer = ' ', int $spacesCount = 4): string
{
    $colors = getColors($theme);
    $result = getStylish($tree, $replacer, $spacesCount, $colors);
    return "{\n{$result}}";
}

/**
 * @throws Exception
 */
function getStylish(array $tree, string $replacer, int $spacesCount, $colors, int $depth = 1): string
{
    return array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $replacer, $spacesCount, $colors, $depth) {
            $keyData = $tree[$key];
            $statusName = $tree[$key]['status'] ?? 'add';
            $resultString = $acc;

            $status = match ($statusName) {
                'add' => ADD_MARKER,
                'remove' => REMOVE_MARKER,
                default => UNCHANGED_MARKER
            };

            $indentationCount = $spacesCount * $depth - 2;
            $indentation = str_repeat($replacer, $indentationCount);

            if (array_key_exists('children', $keyData)) {
                $value = $keyData['children'];

                if ($status === ADD_MARKER) {
                    $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1, $colors);
                    $string = getStylishInnerContent($status, $key, $innerContent, $indentation, $colors);
                    return "{$resultString}{$string}";
                }

                $innerContent = getStylish($value, $replacer, $spacesCount, $colors, $depth + 1);
                $string = getStylishInnerContent($status, $key, $innerContent, $indentation, $colors);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = $keyData['value'];
                $string = getStylishString($status, $key, $value, $indentation, $colors);
                return "{$resultString}{$string}";
            }

            $data = [
                'indentation' => $indentation,
                'replacer' => $replacer,
                'spacesCount' => $spacesCount,
                'depth' => $depth
            ];

            $stringBefore = getChangedString(
                REMOVE_MARKER,
                $keyData['beforeValue'],
                $key,
                $data,
                $colors
            );

            $stringAfter = getChangedString(
                ADD_MARKER,
                $keyData['afterValue'],
                $key,
                $data,
                $colors
            );

            return "{$resultString}{$stringBefore}{$stringAfter}";
        },
        ''
    );
}

/**
 * @throws Exception
 */
function getArrayContent(array $tree, string $replacer, int $spacesCount, int $depth, $colors): string
{
    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth, $colors) {
        $indentationCount = $spacesCount * $depth - 2;
        $indentation = str_repeat($replacer, $indentationCount);
        $resultString = $acc;

        if (array_key_exists('value', $tree[$key])) {
            $value = $tree[$key]['value'];
            $string = getStylishString(UNCHANGED_MARKER, $key, $value, $indentation, $colors);
            return "{$resultString}{$string}";
        }

        $value = $tree[$key]['children'];
        $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1, $colors);
        return getStylishInnerContent(UNCHANGED_MARKER, $key, $innerContent, $indentation, $colors);
    }, '');
}

// phpcs:ignore
function getStylishInnerContent(string $marker, int|string $key, string $innerContent, string $indentation, $colors): string
{
    $colorMarker = getMarker($marker, $colors);
    $colorKey = "\033[{$colors['primary']}m{$key}\033[{$colors['end']}m";

    return "{$indentation}{$colorMarker} {$colorKey}: {\n{$innerContent}{$indentation}  }\n";
}

function getStylishString(string $marker, int|string $key, mixed $value, string $indentation, $colors): string
{
    $keyValue = getString($value, $colors);
    $colorMarker = getMarker($marker, $colors);
    $colorKey = "\033[{$colors['primary']}m{$key}\033[{$colors['end']}m";

    return "{$indentation}{$colorMarker} {$colorKey}: {$keyValue}\n";
}

/**
 * @throws Exception
 */
function getChangedString(string $marker, mixed $value, int|string $key, array $data, $colors): string
{
    if (!is_array($value)) {
        $result = getStylishString($marker, $key, $value, $data['indentation'], $colors);
    } else {
        $innerContent = getStylish($value, $data['replacer'], $data['spacesCount'], $colors, $data['depth'] + 1);
        $result = getStylishInnerContent($marker, $key, $innerContent, $data['indentation'], $colors);
    }

    return $result;
}

function getString(mixed $string, $colors): string
{
    if (is_bool($string)) {
        // phpcs:ignore
        return $string ? "\033[{$colors['special']}mtrue\033[{$colors['end']}m" : "\033[{$colors['special']}mfalse\033[{$colors['end']}m";
    }

    if (is_null($string)) {
        return "\033[{$colors['special']}mnull\033[{$colors['end']}m";
    }

    if (is_numeric($string)) {
        return "\033[{$colors['number']}m{$string}\033[{$colors['end']}m";
    }

    if (is_string($string)) {
        return "\033[{$colors['string']}m'{$string}'\033[{$colors['end']}m";
    }

    return $string;
}

function getMarker(string $marker, $colors): string
{
    if ($marker === ADD_MARKER) {
        return "\033[{$colors['add']}m{$marker}\033[{$colors['end']}m";
    }

    if ($marker === REMOVE_MARKER) {
        return "\033[{$colors['remove']}m{$marker}\033[{$colors['end']}m";
    }

    return $marker;
}
