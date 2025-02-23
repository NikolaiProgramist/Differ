<?php

namespace Differ\Formatters\Stylish;

const ADD_MARKER = '+';
const REMOVE_MARKER = '-';
const UNCHANGED_MARKER = ' ';

function stylish(array $tree, string $replacer = ' ', int $spacesCount = 4): string
{
    $result = getStylish($tree, $replacer, $spacesCount);
    return "{\n{$result}}";
}

function getStylish(array $tree, string $replacer, int $spacesCount, int $depth = 1): string
{
    return array_reduce(
        array_keys($tree),
        function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
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
                    $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
                    $string = getStylishInnerContent($status, $key, $innerContent, $indentation);
                    return "{$resultString}{$string}";
                }

                $innerContent = getStylish($value, $replacer, $spacesCount, $depth + 1);
                $string = getStylishInnerContent($status, $key, $innerContent, $indentation);
                return "{$resultString}{$string}";
            }

            if (array_key_exists('value', $keyData)) {
                $value = $keyData['value'];
                $string = getStylishString($status, $key, $value, $indentation);
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
                $data
            );

            $stringAfter = getChangedString(
                ADD_MARKER,
                $keyData['afterValue'],
                $key,
                $data
            );

            return "{$resultString}{$stringBefore}{$stringAfter}";
        },
        ''
    );
}

function getArrayContent(array $tree, string $replacer, int $spacesCount, int $depth): string
{
    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree, $replacer, $spacesCount, $depth) {
        $indentationCount = $spacesCount * $depth - 2;
        $indentation = str_repeat($replacer, $indentationCount);
        $resultString = $acc;

        if (array_key_exists('value', $tree[$key])) {
            $value = $tree[$key]['value'];
            $string = getStylishString(UNCHANGED_MARKER, $key, $value, $indentation);
            return "{$resultString}{$string}";
        }

        $value = $tree[$key]['children'];
        $innerContent = getArrayContent($value, $replacer, $spacesCount, $depth + 1);
        return getStylishInnerContent(UNCHANGED_MARKER, $key, $innerContent, $indentation);
    }, '');
}

function getStylishInnerContent(string $marker, int|string $key, string $innerContent, string $indentation): string
{
    return "{$indentation}{$marker} {$key}: {\n{$innerContent}{$indentation}  }\n";
}

function getStylishString(string $marker, int|string $key, mixed $value, string $indentation): string
{
    $keyValue = getString($value);
    return "{$indentation}{$marker} {$key}: {$keyValue}\n";
}

function getChangedString(string $marker, mixed $value, int|string $key, array $data): string
{
    if (!is_array($value)) {
        $result = getStylishString($marker, $key, $value, $data['indentation']);
    } else {
        $innerContent = getStylish($value, $data['replacer'], $data['spacesCount'], $data['depth'] + 1);
        $result = getStylishInnerContent($marker, $key, $innerContent, $data['indentation']);
    }

    return $result;
}

function getString(mixed $string): string
{
    if (is_bool($string)) {
        return $string ? 'true' : 'false';
    }

    if (is_null($string)) {
        return 'null';
    }

    return $string;
}
