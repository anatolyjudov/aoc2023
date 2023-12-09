<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$sum1 = $sum2 =  0;
foreach ($inputLines as $line) {
    $seq = explode(' ', $line);
    $sum1 += extrapolate($seq);
    $sum2 += extrapolate(array_reverse($seq));
}

echo $sum1 . "\r\n";
echo $sum2 . "\r\n";

function extrapolate(array $seq): int {
    $prods = [$seq];

    do $prods[] = diff($prods[sizeof($prods) - 1]);
    while (!is_zero($prods[sizeof($prods) - 1]));

    $prods[array_key_last($prods)][] = 0;

    for($p = sizeof($prods) - 2; $p >= 0; $p--) {
        $new_value = end($prods[$p]) + end($prods[$p + 1]);
        $prods[$p][] = $new_value;
    }

    return end($prods[0]);
}

function diff(array $seq): array {
    $diff = [];

    for($i = 0; $i < sizeof($seq) - 1; $i++)
        $diff[$i] = $seq[$i + 1] - $seq[$i];

    return $diff;
}

function is_zero(array $seq): bool {
    for($i = 0; $i < sizeof($seq); $i++)
        if ($seq[$i] !== 0) return false;

    return true;
}