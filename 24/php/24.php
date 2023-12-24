<?php

//$range = [7, 27];
$range = [200000000000000, 400000000000000];
$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$hails = [];

foreach($inputLines as $inputLine) {
    list($pos_str, $speed_str) = explode(' @ ', $inputLine);
    $pos = explode(', ', $pos_str);
    $speed = explode(', ', $speed_str);
    $hails[] = [$pos, $speed];
}

//print_r($hails);

$sum = 0;
for($i = 0; $i < sizeof($hails) - 1; $i++) {
    for($ii = $i + 1; $ii < sizeof($hails); $ii++) {
        $collide = will_path_instersect($hails[$i], $hails[$ii]);
        if ($collide !== false) {
            if (($collide[0] >= $range[0] && $collide[0] <= $range[1]
                && $collide[1] >= $range[0] && $collide[1] <= $range[1])
            ) {
                if (($collide[2][0] > 0) && ($collide[2][1] > 0)) $sum++;
            }
        }
    }
}

echo $sum . PHP_EOL;

function will_path_instersect($a, $b) {

    // t = (x - x0) / xs
    // y = y0 + ys * (x - x0) / xs
    // y * xs = y0 * xs + ys * x - ys * x0
    // y * xs - x * ys = y0 * xs - ys * x0

    $A = [
        [$a[1][1], -1 * $a[1][0]],
        [$b[1][1], -1 * $b[1][0]],
    ];

    $B = [
        [$a[0][0] * $a[1][1] - $a[0][1] * $a[1][0]],
        [$b[0][0] * $b[1][1] - $b[0][1] * $b[1][0]],
    ];

    $det = $A[0][0] * $A[1][1] - $A[1][0] * $A[0][1];
    if ($det === 0) {
        // let's speculate we don't have hails with the same path
        return false;
    }

    $Ar = [
        [$A[1][1], -1 * $A[0][1]],
        [-1 * $A[1][0], $A[0][0]],
    ];

    $X = [];
    foreach($Ar as $row) {
        $sum = 0;
        for($i = 0; $i < sizeof($row); $i++) {
            $sum += $row[$i] * $B[$i][0];
        }
        $X[] = $sum / $det;
    }

    // $t = (x - x0) / xs
    $t = [];
    $t[0] = ($X[0] - $a[0][0]) / $a[1][0];
    $t[1] = ($X[0] - $b[0][0]) / $b[1][0];

    $X[] = $t;

    return $X;
}