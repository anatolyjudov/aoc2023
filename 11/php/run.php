<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$galaxies = $void_lines = $void_cols = [];

for ($y = 0; $y < sizeof($inputLines); $y++)
    if (!str_contains($inputLines[$y], '#'))
        $void_lines[] = $y;
    else for($x = 0; $x < strlen($inputLines[$y]); $x++)
        if ($inputLines[$y][$x] !== '.')
            $galaxies[] = [$y, $x];

$xs = array_column($galaxies, 1);

for($x = 0; $x < strlen($inputLines[0]); $x++)
    if (!in_array($x, $xs))
        $void_cols[] = $x;

$sum1 = $sum10e6 = 0;

for ($i = 0; $i < sizeof($galaxies) - 1; $i++)
    for ($ii = $i + 1; $ii < sizeof($galaxies); $ii++) {
        $y0 = min($galaxies[$i][0], $galaxies[$ii][0]);
        $y1 = max($galaxies[$i][0], $galaxies[$ii][0]);
        $x0 = min($galaxies[$i][1], $galaxies[$ii][1]);
        $x1 = max($galaxies[$i][1], $galaxies[$ii][1]);

        $yv = sizeof(array_filter($void_lines, function ($n) use ($y0, $y1) {
            return $n > $y0 && $n < $y1;
        }));

        $xv = sizeof(array_filter($void_cols, function ($n) use ($x0, $x1) {
            return $n > $x0 && $n < $x1;
        }));

        $sum1 += $y1 - $y0 + $yv + $x1 - $x0 + $xv;
        $sum10e6 += $y1 - $y0 + $yv * 999999 + $x1 - $x0 + $xv * 999999;
    }

echo $sum1 . PHP_EOL;
echo $sum10e6 . PHP_EOL;