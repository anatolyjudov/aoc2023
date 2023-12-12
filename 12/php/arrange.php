<?php

$inputLines = file('input-test.txt', FILE_IGNORE_NEW_LINES);

$records = [];

foreach ($inputLines as $line) {
    list($record, $control_str) = explode(' ', $line);

    $control = explode(',', $control_str);

    $records[] = [
        'control' => $control,
        'record' => $record
    ];
}

$sum = 0;
$calls_count = 0;
foreach ($records as $k => $record) {
    $arranges = 0;
    foreach(get_options($record['record']) as $option) {
        $options_groups = array_values(
            array_map('strlen', get_parts($option))
        );
        if ($options_groups == $record['control']) {
            $arranges++;
        }
    }
    $records[$k]['arranges'] = $arranges;
    $sum += $arranges;
}

echo $sum . PHP_EOL;
echo $calls_count . PHP_EOL;

function get_options(string $record)
{
    global $calls_count;
    $calls_count++;

    echo $record . ' ';

    if (str_contains($record, '?')) {
        $pos = strpos($record, '?');
        $last_part = substr($record, $pos + 1);
        foreach (get_options($last_part) as $option) {
            yield substr($record, 0, $pos) . '.' . $option;
            yield substr($record, 0, $pos) . '#' . $option;
        }
    } else {
        yield $record;
    }
}

function get_parts(&$array)
{
    return array_filter(
        explode('.', $array),
        function($part) {
            return $part !== '';
        }
    );
}