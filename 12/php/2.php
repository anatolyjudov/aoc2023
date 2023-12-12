<?php
// https://www.youtube.com/@hyper-neutrino

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$cache = [];

$sum = 0;
foreach ($inputLines as $line) {
    list($record, $control_str) = explode(' ', $line);

    $record = substr(str_repeat($record . '?', 5), 0, -1);
    $control_str = substr(str_repeat($control_str . ',', 5), 0, -1);

    $control = array_map(function ($i) {
        return (int)$i;
    }, explode(',', $control_str));


    $sum += get_count($record, $control);
    echo $record . PHP_EOL . $sum . PHP_EOL . 'cache size: ' . sizeof($cache) . PHP_EOL;
}

echo $sum . PHP_EOL;

function get_count(string $input, array $counts): int
{
    global $cache;

    if ($input === '') {
        if (sizeof($counts) === 0) {
            return 1;
        } else {
            return 0;
        }
    }

    if (sizeof($counts) === 0) {
        if (str_contains($input, '#')) {
            return 0;
        } else {
            return 1;
        }
    }

    $cacheKey = implode('-', $counts) . '-' . $input;
    if (isset($cache[$cacheKey])) return $cache[$cacheKey];

    $result = 0;

    if (in_array($input[0], ['#', '?'])) {
        if (
            ($counts[0] <= strlen($input))
            && (!str_contains(substr($input, 0, $counts[0]), '.'))
            && (($counts[0] === strlen($input)) || ($input[$counts[0]] !== '#'))
        ) {
            $result += get_count(
                substr($input, $counts[0] + 1),
                array_slice($counts, 1)
            );
        }
    }

    if (in_array($input[0], ['.', '?'])) {
        $result += get_count(substr($input, 1), $counts);
    }

    if ($cacheKey) {
        $cache[$cacheKey] = $result;
        if (sizeof($cache) % 100 === 0) {
            echo 'cache size: ' . sizeof($cache) . PHP_EOL;
        }
    }

    return $result;
}