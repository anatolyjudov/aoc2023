<?php

const UP = [-1, 0];
const DOWN = [1, 0];
const RIGHT = [0, 1];
const LEFT = [0, -1];
const START = [RIGHT, DOWN];

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$grid = array_map('str_split', $inputLines);
$Y = sizeof($grid);
$X = sizeof($grid[0]);

$heat = [0 => [0 => ['START' => [0 => 0]]]];

do {
    $there_were_changes = false;

    for($y = 0; $y < $Y; $y++) {
        for($x = 0; $x < $X; $x++) {

            foreach([UP, LEFT, DOWN, RIGHT] as $look_dir) {
                $look_dir_str = stringify($look_dir);

                $yp = $y + $look_dir[0];
                $xp = $x + $look_dir[1];

                if (($xp < 0) || ($xp === $X) || ($yp < 0) || ($yp === $Y)) continue;

                if (!isset($heat[$yp][$xp])) {
                    $there_were_changes = true;
                    continue;
                }

                $look = $heat[$yp][$xp];

                foreach($look as $pre_look_dir_str => $strikes) {
                    $reverse_look_dir = [$look_dir[0] * -1, $look_dir[1] * -1];
                    if (unstringify($pre_look_dir_str) === $reverse_look_dir) {
                        continue;
                    }

                    foreach($strikes as $strike => $pre_heat) {
                        if (($look_dir_str === $pre_look_dir_str) && ($strike === 3)) {
                            continue;
                        }

                        $try_heat = $pre_heat + $grid[$y][$x];

                        if ($look_dir_str === $pre_look_dir_str) {
                            $try_strike = $strike + 1;
                        } else {
                            $try_strike = 1;
                        }

                        if (!isset($heat[$y][$x][$look_dir_str][$try_strike]) || ($heat[$y][$x][$look_dir_str][$try_strike] > $try_heat)) {
                            $heat[$y][$x][$look_dir_str][$try_strike] = $try_heat;
                            $there_were_changes = true;
                        }
                    }
                }
            }
        }
    }

} while ($there_were_changes);

echo lowest_heat($Y - 1, $X - 1) . PHP_EOL;

function stringify(array $dir): string {
    return implode(',', $dir);
}

function unstringify(string $dir): array {
    if ($dir === 'START') return [];
    list($dy, $dx) = explode(',', $dir);
    return [(int)$dy, (int)$dx];
}

function lowest_heat($y, $x) {
    global $heat;

    if (!isset($heat[$y][$x])) return null;

    foreach($heat[$y][$x] as $dir => $strikes)
        foreach($strikes as $strike => $that_heat)
            $res = empty($res) ? $that_heat : min($res, $that_heat);

    return $res;
}