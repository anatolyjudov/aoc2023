<?php

use Phpml\Math\Matrix;

require_once __DIR__ . '/vendor/autoload.php';

$range = [200000000000000, 400000000000000];
$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$hails = [];

foreach($inputLines as $inputLine) {
    list($pos_str, $speed_str) = explode(' @ ', $inputLine);
    $pos = explode(', ', $pos_str);
    $speed = explode(', ', $speed_str);
    $hails[] = [$pos, $speed];
}

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
    // -ys * x + xs * y = y0 * xs - ys * x0
    // ys * x - xs * y = ys * x0 - y0 * xs

    $A = new Matrix([
        [$a[1][1], -1 * $a[1][0]],
        [$b[1][1], -1 * $b[1][0]],
    ]);

    $B = new Matrix([
        [$a[0][0] * $a[1][1] - $a[0][1] * $a[1][0]],
        [$b[0][0] * $b[1][1] - $b[0][1] * $b[1][0]],
    ]);

    if ($A->getDeterminant() == 0) {
        return false;
    }

    $X = $A->inverse()->multiply($B)->getColumnValues(0);

    $t = [
        ($X[0] - $a[0][0]) / $a[1][0],
        ($X[0] - $b[0][0]) / $b[1][0]
    ];

    $X[] = $t;

    return $X;
}