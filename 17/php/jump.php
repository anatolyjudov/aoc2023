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

// [y][x][from dir][with strike] => best heat
$heat = [];

$heat = [0 => [0 => ['START' => [0 => 0]]]];

do {

    $there_were_changes = false;

    for($y = 0; $y < $Y; $y++) {
        for($x = 0; $x < $X; $x++) {

            //echo "Tile $y, $x:" . PHP_EOL;

            foreach([UP, LEFT, DOWN, RIGHT] as $look_dir) {
                $look_dir_str = stringify($look_dir);

                $yp = $y + $look_dir[0];
                $xp = $x + $look_dir[1];

                if (($xp < 0) || ($xp === $X) || ($yp < 0) || ($yp === $Y)) continue;

                //echo "- checking " . str_dir($look_dir_str) . PHP_EOL;

                if (!isset($heat[$yp][$xp])) {
                    //echo '- - it doesnt exist yet' . PHP_EOL;
                    $there_were_changes = true;
                    continue;
                }

                $look = $heat[$yp][$xp];

                foreach($look as $pre_look_dir_str => $strikes) {
                    $reverse_look_dir = [$look_dir[0] * -1, $look_dir[1] * -1];
                    if (unstringify($pre_look_dir_str) === $reverse_look_dir) {
                        continue;
                    }
                    //echo "- - has heats from " . str_dir($pre_look_dir_str) . PHP_EOL;

                    foreach($strikes as $strike => $pre_heat) {
                        //echo "- - - heat $pre_heat with strike $strike" . PHP_EOL;
                        if (($look_dir_str === $pre_look_dir_str) && ($strike === 10)) {
                            //echo "- - - - strike too long" . PHP_EOL;
                            continue;
                        }

                        if (($pre_look_dir_str !== 'START') && ($look_dir_str !== $pre_look_dir_str) && ($strike < 4)) {
                            continue;
                        }

                        $try_heat = $pre_heat + $grid[$y][$x];

                        if ($look_dir_str === $pre_look_dir_str) {
                            $try_strike = $strike + 1;
                        } else {
                            $try_strike = 1;
                        }

                        //echo "- - - could be heat " . $try_heat ." with strike $try_strike" . PHP_EOL;

                        if (!isset($heat[$y][$x][$look_dir_str][$try_strike]) || ($heat[$y][$x][$look_dir_str][$try_strike] > $try_heat)) {
                            //echo "- - - - set $y,$x,($look_dir_str),$strike = $try_heat". PHP_EOL;
                            $heat[$y][$x][$look_dir_str][$try_strike] = $try_heat;
                            $there_were_changes = true;
                        }
                    }
                }

            }

        }
    }

} while ($there_were_changes);

print_r($heat[$Y - 1][$X - 1]);

list($lowest_heat, $dir) = lowest_heat($Y - 1, $X - 1);

echo $lowest_heat . PHP_EOL;

/*
for($y = 0; $y < $Y; $y++) {
    for($x = 0; $x < $X; $x++) {
        list($lowest_heat, $dir) = lowest_heat($y, $x);
        echo str_pad($lowest_heat, 4, ' ', STR_PAD_LEFT);
        echo ' (' . $grid[$y][$x] . ') ';
        
        echo ' ' . match($dir) {
            '-1,0' => '^',
            '1,0' => 'v',
            '0,-1' => '<',
            '0,1' => '>',
            'START' => 'S'
        } . ' ';

    }
    echo PHP_EOL;
}
echo PHP_EOL;
*/

function str_dir(string $dir)
{
    return match($dir) {
        '-1,0' => '^',
        '1,0' => 'v',
        '0,-1' => '<',
        '0,1' => '>',
        'START' => 'S'
    };
}

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

    $res = 99999;
    $res_dir = [];
    foreach($heat[$y][$x] as $dir => $strikes) {
        foreach($strikes as $strike => $that_heat) {
            $res = min($res, $that_heat);
            if ($res === $that_heat) {
                $res_dir = $dir;
            }
        }
    }

    return [$res, $res_dir];
}