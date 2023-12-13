<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$patterns = [];
$p = $sum = 0;

foreach ($inputLines as $inputLine) {
    if ($inputLine === '') {
        $p++;
        continue;
    }

    $patterns[$p][] = $inputLine;
}

foreach ($patterns as $pattern_num => $pattern) {
    $p = ['h' => [], 'v' => []];
    
    foreach ($pattern as $line) {
        $p['h'][] = bindec(str_replace(['.', '#'], ['0', '1'], $line));

    }

    $rotated = rotate($pattern);
    foreach ($rotated as $line) {
        $p['v'][] = bindec(str_replace(['.', '#'], ['0', '1'], implode('', $line)));
    }

    foreach ($p as $type => $codes) {
        for ($i = 0; $i < sizeof($codes) - 1; $i++) {
            $slice1 = array_reverse(array_slice($codes, 0, $i + 1));
            $slice2 = array_slice($codes, $i + 1);
            $size = min(sizeof($slice1), sizeof($slice2));
            $slice1 = array_slice($slice1, 0, $size);
            $slice2 = array_slice($slice2, 0, $size);
            if ($slice1 === $slice2) {
                $sum += ($i + 1) * (($type === 'h') ? 100 : 1);
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