<?php

$tiles = [
    '^' => [-1,  0],
    '>' => [ 0,  1],
    'v' => [ 1,  0],
    '<' => [ 0, -1]
];

$grid = file('input.txt', FILE_IGNORE_NEW_LINES);

$Y = sizeof($grid);
$X = strlen($grid[0]);

for ($y = 0; $y < $Y; $y++) {
    $grid[$y] = str_split($grid[$y]);
}

$start = [0, strpos(implode('', $grid[0]), '.')];
$end = [$Y - 1, strpos(implode('', $grid[$Y - 1]), '.')];

$seen[$start[0]][$start[1]] = 1;

$longest = get_longest(0, $start, $seen);

echo 'Part 1: ' . $longest . PHP_EOL;

function get_longest($length, $current, $seen) {
    global $end, $grid, $tiles, $X, $Y;

    $y = $current[0];
    $x = $current[1];

    if ([$y, $x] === $end) {
        return $length;
    }

    if (in_array($grid[$y][$x], ['<', '>', '^', 'v'])) {
        $options = [$tiles[$grid[$y][$x]]];
    } else {
        $options = [[1, 0], [-1, 0], [0, 1], [0, -1]];
    }

    $max_length = 0;
    foreach ($options as $step) {
        $yn = $y + $step[0];
        $xn = $x + $step[1];

        if ($yn < 0 || $yn === $Y || $xn < 0 || $xn === $X) {
            continue;
        }

        if ($grid[$yn][$xn] === '#') continue;

        if (isset($seen[$yn][$xn])) continue;

        $new_seen = $seen;
        $new_seen[$yn][$xn] = 1;
        $try_length = get_longest($length + 1, [$yn, $xn], $new_seen);
        if ($max_length < $try_length) {
            $max_length = $try_length;
        }
    }

    return $max_length;
}