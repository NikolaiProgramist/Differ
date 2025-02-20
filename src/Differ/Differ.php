<?php

namespace Differ\Differ;

use Exception;

use function Functional\sort;
use function Differ\Parser\parse;
use function Differ\Parser\parseToJson;

/**
 * @throws Exception
 */
function genDiff(
    string $pathToFile1,
    string $pathToFile2,
    string $format = 'stylish',
    string $theme = 'default'
): string {
    $firstFile = parseToJson($pathToFile1);
    $secondFile = parseToJson($pathToFile2);

    $elements = sortingFirstFile($firstFile, $secondFile);
    $elements2 = sortingSecondFile($secondFile, $firstFile);

    $resultDiff = sortArrayByKeysRecursive(array_merge_recursive($elements, $elements2));
    return parse($resultDiff, $format, $theme);
}

function sortingFirstFile(mixed $tree1, mixed $tree2): mixed
{
    if (!is_array($tree1)) {
        return $tree1;
    }

    if (!is_array($tree2)) {
        return $tree2;
    }

    return array_reduce(
        array_keys($tree1),
        function ($acc, $key) use ($tree1, $tree2) {
            if (!array_key_exists($key, $tree2)) {
                $innerContent = sortingFirstFile($tree1[$key], $tree1[$key]);
                $valueName = is_array($innerContent) ? 'children' : 'value';
                return array_merge($acc, [$key => ['status' => 'remove', $valueName => $innerContent]]);
            }

            if ($tree1[$key] === $tree2[$key]) {
                $innerContent = sortingFirstFile($tree1[$key], $tree1[$key]);
                $valueName = is_array($innerContent) ? 'children' : 'value';
                return array_merge($acc, [$key => ['status' => 'unchanged', $valueName => $innerContent]]);
            }

            if (is_array($tree1[$key]) && is_array($tree2[$key])) {
                $innerContent = sortingFirstFile($tree1[$key], $tree2[$key]);
                return array_merge($acc, [$key => ['status' => 'changed', 'children' => $innerContent]]);
            } else {
                $beforeValue = sortingFirstFile($tree1[$key], $tree1[$key]);
                $afterValue = sortingFirstFile($tree2[$key], $tree2[$key]);

                return array_merge(
                    $acc,
                    [
                        $key => [
                            'status' => 'remove',
                            'beforeValue' => $beforeValue,
                            'afterValue' => $afterValue
                        ]
                    ]
                );
            }
        },
        []
    );
}

function sortingSecondFile(mixed $tree2, mixed $tree1): mixed
{
    if (!is_array($tree2)) {
        return $tree2;
    }

    return array_reduce(
        array_keys($tree2),
        function ($acc, $key) use ($tree1, $tree2) {
            if (!array_key_exists($key, $tree1)) {
                $valueName = is_array(getArrayContent($tree2[$key])) ? 'children' : 'value';
                return array_merge($acc, [$key => [$valueName => getArrayContent($tree2[$key])]]);
            }

            if (is_array($tree2[$key]) && is_array($tree1[$key])) {
                return array_merge($acc, [$key => ['children' => sortingSecondFile($tree2[$key], $tree1[$key])]]);
            }

            return $acc;
        },
        []
    );
}

function getArrayContent(mixed $tree): mixed
{
    if (!is_array($tree)) {
        return $tree;
    }

    return array_reduce(array_keys($tree), function ($acc, $key) use ($tree) {
        $valueName = is_array(getArrayContent($tree[$key])) ? 'children' : 'value';
        return array_merge($acc, [$key => [$valueName => getArrayContent($tree[$key])]]);
    }, []);
}

function sortArrayByKeysRecursive(array $tree): array
{
    $keysSorted = sort(array_keys($tree), fn($left, $right) => strcmp($left, $right), true);

    return array_reduce($keysSorted, function ($acc, $key) use ($tree) {
        if (!is_array($tree[$key])) {
            return array_merge($acc, [$key => $tree[$key]]);
        }

        $innerContent = sortArrayByKeysRecursive($tree[$key]);
        return array_merge($acc, [$key => $innerContent]);
    }, []);
}
