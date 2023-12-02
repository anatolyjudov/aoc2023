<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);
$calibrationValues = [];

foreach ($inputLines as $line) {
    $d1 = $line[strcspn($line,'0123456789')];
    $line = strrev($line);
    $d2 = $line[strcspn($line,'0123456789')];
    $calibrationValues[] = (int)($d1 . $d2);
}

print_r(array_sum($calibrationValues));