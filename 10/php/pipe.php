<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$pipes = [
    '|' => [[-1,  0], [ 1, 0]],
    '-' => [[ 0, -1], [ 0, 1]],
    'L' => [[-1,  0], [ 0, 1]],
    'J' => [[ 0, -1], [-1, 0]],
    '7' => [[ 0, -1], [ 1, 0]],
    'F' => [[ 1,  0], [ 0, 1]],
    'S' => [[-1,  0], [ 1, 0], [0, -1], [0, 1]]
];

$map = [];

$X = $Y = 0;
$start = [];

foreach($inputLines as $line) {
    $map[] = str_split($line);
    $X = max($X, strlen($line));

    if (str_contains($line, 'S') !== false) {
        $start_node = [
            'y' => $Y,
            'x' => strpos($line, 'S'),
            'pipes' => $pipes['S']
        ];
    }

    $Y++;
}

$nodes = [];
$distances[$start_node['y']][$start_node['x']] = 0;

set_distance($start_node, 0, $distances, $map, $pipes, $Y, $X);
function set_distance($node, $distance, &$distances, &$map, &$pipes, $Y, $X)
{
    $distances[$node['y']][$node['x']] = $distance;

    foreach($node['pipes'] as $pipe) {
        $ny = $node['y'] + $pipe[0];
        $nx = $node['x'] + $pipe[1];

        if ($nx < 0 || $ny < 0 || $nx === $X || $ny === $Y) continue;
        if (in_array($map[$ny][$nx], ['.', 'S'])) continue;
        if (isset($distances[$ny][$nx]) && $distances[$ny][$nx] <= $distance + 1) continue;

        $next_node = [
            'y' => $ny,
            'x' => $nx,
            'pipes' => $pipes[$map[$ny][$nx]]
        ];

        $compatible = false;
        foreach ($next_node['pipes'] as $next_node_pipe) {
            if ($next_node_pipe[0] === -1 * $pipe[0] && $next_node_pipe[1] === -1 * $pipe[1]) {
                $compatible = true;
                break;
            }
        }
        if ($compatible === false) continue;

        set_distance($next_node, $distance + 1, $distances, $map, $pipes, $Y, $X);
    }
}

$max_distance = 0;
for($y = 0; $y < $Y; $y++) {
    for($x = 0; $x < $X; $x++) {
        if (isset($distances[$y][$x])) {
            echo str_pad($distances[$y][$x], 5, ' ', STR_PAD_LEFT);
            $max_distance = max($max_distance, $distances[$y][$x]);
        } else {
            echo '    .';
        }

    }
    echo PHP_EOL;
}

echo $max_distance . PHP_EOL . PHP_EOL;

$new_map = [];
for($y = 0; $y < $Y; $y++) {
    for ($x = 0; $x < $X; $x++) {
        if (!isset($distances[$y][$x])) continue;

        $new_map[$y * 3 + 1][$x * 3 + 1] = '#';
        foreach($pipes[$map[$y][$x]] as $dir) {
            $cy = $y * 3 + 1 + $dir[0];
            $cx = $x * 3 + 1 + $dir[1];
            $new_map[$cy][$cx] = '#';
        }
    }
}

$opens = [];
$X3 = $X * 3;
$Y3 = $Y * 3;

check_open(0, 0, $new_map, $opens, $Y3, $X3);
function check_open($y, $x, &$new_map, &$opens, $Y3, $X3) {

    if (isset($opens[$y][$x]) || isset($new_map[$y][$x])) return;

    $opens[$y][$x] = true;

    foreach ([[-1,  0], [ 1, 0], [0, -1], [0, 1]] as $dir) {
        $cy = $y + $dir[0];
        $cx = $x + $dir[1];

        if ($cx < 0 || $cy < 0 || $cx === $X3 || $cy === $Y3) continue;

        check_open($cy, $cx, $new_map, $opens, $Y3, $X3);
    }
}

$inside = 0;
for($y = 0; $y < $Y3; $y++) {
    for($x = 0; $x < $X3; $x++) {
        if (isset($new_map[$y][$x])) {
            echo $new_map[$y][$x];
        } elseif (isset($opens[$y][$x]) && $opens[$y][$x] === true) {
            echo 'O';
        } else {
            if ((($y - 1) % 3 === 0) && (($x - 1) % 3 === 0)) {
                $inside++;
                echo 'I';
            } else {
                echo '.';
            }
        }

    }
    echo "\r\n";
}

echo $inside . PHP_EOL;