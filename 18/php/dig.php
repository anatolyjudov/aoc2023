<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$grid = [];

$trenches = [];

$start = [0, 0];
$Xmin = $Xmax = $Ymin = $Ymax = 0;

foreach($inputLines as $n => $line) {
    $data = explode(' ', $line);
    $trenches[$n] = [
        'dir' => $data[0],
        'dist' => $data[1],
        'rgb' => str_split(substr($data[2], 2, -1), 2),
        'start' => $start,
    ];
    $end[0] = $start[0] + match($data[0]) {'U' => -1, 'D' => 1, 'L', 'R' => 0} * $data[1];
    $end[1] = $start[1] + match($data[0]) {'U', 'D' => 0, 'L' => -1, 'R' => 1} * $data[1];
    $trenches[$n]['end'] = $end;

    $Ymax = max($Ymax, $end[0], $start[0]);
    $Ymin = min($Ymin, $end[0], $start[0]);

    $Xmax = max($Xmax, $end[1], $start[1]);
    $Xmin = min($Xmin, $end[1], $start[1]);

    $start = $end;
}

foreach($trenches as $n => $trench) {
    for($y = min($trench['start'][0], $trench['end'][0]); $y <= max($trench['start'][0], $trench['end'][0]); $y = $y + 1) {
        for($x = min($trench['start'][1], $trench['end'][1]); $x <= max($trench['start'][1], $trench['end'][1]); $x = $x + 1) {
            $grid[$y - $Ymin][$x - $Xmin] = $n; 
        }
    }
}

// count
$sum = 0;
for($y = 0; $y <= ($Ymax - $Ymin); $y++) {
    $in = false;
    $riding = false;
    $riding_hor = false;
    for($x = 0; $x <= ($Xmax - $Xmin); $x++) {
        if (isset($grid[$y][$x])) {
            if ($riding === false) {
                $riding = true;
            }
            if ($riding && ($riding_hor === false)) {
                if (in_array($trenches[$grid[$y][$x]]['dir'], ['L', 'R'])) {
                    $riding_hor = $grid[$y][$x];
                }
            }
            $sum++;
        } else {
            if ($riding) {
                if ($riding_hor === false) {
                    $in = !$in;
                } else {
                    $left_trench_num = (($riding_hor - 1) > 0) ? ($riding_hor - 1) : (sizeof($trenches) - 1);
                    $right_trench_num = (($riding_hor + 1) < (sizeof($trenches))) ? ($riding_hor + 1) : 0;
                    if ($trenches[$left_trench_num]['dir'] === $trenches[$right_trench_num]['dir']) {
                        $in = !$in;
                    }
                }
                $riding = false;
                $riding_hor = false;
            }
            if ($in) {
                $sum++;
            }
        }

    }
}

echo $sum . PHP_EOL;