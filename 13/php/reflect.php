<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$patterns = [];
$p = 0;

foreach ($inputLines as $inputLine) {
    if ($inputLine === '') {
        $p++;
        continue;
    }

    $patterns[$p][] = $inputLine;
}

$pats = [];
foreach ($patterns as $pattern) {
    $hors = [];
    foreach ($pattern as $line) {
        $hors[] = bindec(str_replace(['.', '#'], ['0', '1'], $line));
    }
    $vers = [];
    $rotated = rotate($pattern);
    foreach ($rotated as $line) {
        $vers[] = bindec(str_replace(['.', '#'], ['0', '1'], implode('', $line)));
    }
    $pats[] = [
        'hors' => $hors,
        'vers' => $vers
    ];
}

//print_r($pats);

$sum = 0;
foreach ($pats as $pat) {
    foreach ($pat as $type => $h) {
        for ($i = 1; $i < sizeof($h) - 1; $i++) {
            $slice1 = array_reverse(array_slice($h, 0, $i + 1));
            $slice2 = array_slice($h, $i + 1);
            $size = min(sizeof($slice1), sizeof($slice2));
            $slice1 = array_slice($slice1, 0, $size);
            $slice2 = array_slice($slice2, 0, $size);
            if ($slice1 === $slice2) {
                $sum += ($i + 1) * (($type === 'hors') ? 100 : 1);
            }
        }
    }
}

echo $sum;


function rotate($arr)
{
    $res = [];
    $pattern = array_map('str_split', $arr);
    for ($y = 0; $y < sizeof($pattern); $y++) {
        for ($x = 0; $x < sizeof($pattern[0]); $x++) {
            $res[$x][$y] = $pattern[$y][$x];
        }
    }
    return $res;
}