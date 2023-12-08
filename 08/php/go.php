<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$lr = str_split($inputLines[0]);

$maps = [];
for($i = 2; $i < sizeof($inputLines); $i++) {
    $maps[substr($inputLines[$i], 0, 3)] = [
        'L' => substr($inputLines[$i], 7, 3),
        'R' => substr($inputLines[$i], 12, 3)
    ];
}

$step = 0;
do {
    $current = $maps[$current ?? 'AAA'][$lr[$step % sizeof($lr)]];
    $step++;
} while ($current !== 'ZZZ');

echo $step;