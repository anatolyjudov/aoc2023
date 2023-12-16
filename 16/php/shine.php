<?php

const UP = 0;
const LEFT = 1;
const DOWN = 2;
const RIGHT = 3;

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$Y = sizeof($inputLines);
$X = strlen($inputLines[0]);
$mirrors = $starting_beams = [];

foreach($inputLines as $y => $line)
    for($x = 0; $x < $X; $x++)
        if ($line[$x] !== '.')
            $mirrors[$y][$x] = $line[$x];

for($x = 0; $x < $X; $x++) {
    $starting_beams[] = [-1, $x, DOWN];
    $starting_beams[] = [$Y, $x, UP];
}

for($y = 0; $y < $Y; $y++) {
    $starting_beams[] = [$y, -1, RIGHT];
    $starting_beams[] = [$y, $X, LEFT];
}

$max_energy = 0;

foreach($starting_beams as $starting_beam) {
    $energy = $seen = [];
    $beams = [$starting_beam];

    while(sizeof($beams) > 0) {
        foreach($beams as $n => $beam) {
            if (isset($seen[$beam[0]][$beam[1]][$beam[2]])) {
                unset($beams[$n]);
                continue;
            }

            $seen[$beam[0]][$beam[1]][$beam[2]] = true;

            $next = [
                $beam[0] + match($beam[2]) {UP => -1, DOWN => 1, default => 0},
                $beam[1] + match($beam[2]) {LEFT => -1, RIGHT => 1, default => 0},
            ];

            if ($next[0] < 0 || $next[0] === $Y || $next[1] < 0 || $next[1] === $X) {
                unset($beams[$n]);
                continue;
            }

            if (!isset($energy[$next[0]][$next[1]]))
                $energy[$next[0]][$next[1]] = true;

            $tile = isset($mirrors[$next[0]][$next[1]]) ? $mirrors[$next[0]][$next[1]] : '.';

            if ($tile === '/') {
                $next[2] = match($beam[2]) {
                    RIGHT => UP, LEFT => DOWN, UP => RIGHT, DOWN => LEFT,
                };
            } else if ($tile === '\\') {
                $next[2] = match($beam[2]) {
                    RIGHT => DOWN, LEFT => UP, UP => LEFT, DOWN => RIGHT,
                };
            } else if ($tile === '.'
                || (($tile === '-') && in_array($beam[2], [LEFT, RIGHT]))
                || (($tile === '|') && in_array($beam[2], [UP, DOWN]))
                ) {
                $next[2] = $beam[2];
            } else if (($tile === '|') && in_array($beam[2], [LEFT, RIGHT])) {
                $next[2] = UP;
                $new_beam = $next;
                $new_beam[2] = DOWN;
                $beams[] = $new_beam;
            } else if (($tile === '-') && in_array($beam[2], [UP, DOWN])) {
                $next[2] = LEFT;
                $new_beam = $next;
                $new_beam[2] = RIGHT;
                $beams[] = $new_beam;
            }

            $beams[$n] = $next;
        }
    }

    $sum = 0;

    for($y = 0; $y < $Y; $y++) for($x = 0; $x < $X; $x++) if (isset($energy[$y][$x])) $sum = empty($sum) ? 1 : $sum + 1;

    $max_energy = max($max_energy, $sum);

    if ($starting_beam === [0, -1, RIGHT]) echo $sum . PHP_EOL;
}

echo $max_energy . PHP_EOL;