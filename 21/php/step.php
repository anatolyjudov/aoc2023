<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$rocks = [];
$start = [];

$Y = sizeof($inputLines);
$X = strlen($inputLines[0]);

for($y = 0; $y < $Y; $y++) {
    for($x = 0; $x < $X; $x++) {
        $line = $inputLines[$y];
        if ($line[$x] === '#') {
            $rocks[$y][$x] = 1;
            continue;
        }
        if ($line[$x] === 'S') {
            $start = [$y, $x];
        }
    }
}

$reaches = [$start];

for($steps = 0; $steps < 64; $steps++) {
    //echo $steps . ' ';
    $new_reaches = [];
    foreach($reaches as $reach) {
        foreach([[-1, 0], [0, -1], [1, 0], [0, 1]] as $move) {
            $yn = $reach[0] + $move[0];
            $xn = $reach[1] + $move[1];
            if (($xn < 0) || ($xn === $X) || ($yn < 0) || ($yn === $Y)) continue;
            if (isset($rocks[$yn][$xn])) continue;
            $new_reaches[$yn][$xn] = isset($new_reaches[$yn][$xn]) ? $new_reaches[$yn][$xn] + 1 : 1;
        }
    }
    
    $reaches = [];
    foreach($new_reaches as $yn => $xns) {
        foreach($xns as $xn => $count) {
            $reaches[] = [$yn, $xn];
        }
    }

    //echo 'After step #' . $steps . ': ' . sizeof($reaches) . PHP_EOL;
    //print_r($reaches);
}

echo 'Part 1: ' . sizeof($reaches) . PHP_EOL;
