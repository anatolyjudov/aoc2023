<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$patterns = [];
$p = $sum1 = $sum2 = 0;

foreach ($inputLines as $inputLine) {
    if ($inputLine === '') {
        $p++;
        continue;
    }

    $patterns[$p][] = $inputLine;
}

foreach ($patterns as $pattern_num => $pattern) {
    $p = ['h' => [], 'v' => []];

    foreach ($pattern as $line)
        $p['h'][] = bindec(str_replace(['.', '#'], ['0', '1'], $line));

    $rotated = rotate($pattern);

    foreach ($rotated as $line)
        $p['v'][] = bindec(str_replace(['.', '#'], ['0', '1'], implode('', $line)));

    $founds = [];

    foreach ($p as $type => $codes) {

        for ($i = 0; $i < sizeof($codes) - 1; $i++) {
            $slice1 = array_reverse(array_slice($codes, 0, $i + 1));
            $slice2 = array_slice($codes, $i + 1);
            $size = min(sizeof($slice1), sizeof($slice2));

            if (array_slice($slice1, 0, $size) === array_slice($slice2, 0, $size)) {
                $sum1 += ($i + 1) * (($type === 'h') ? 100 : 1);
                $founds[$type][] = $i + 1;
            }
        }
    }

    $r = 0;

    foreach(get_patterns($p['h'], $pattern) as $codes) {
        $r = calculate($codes, 'h', $founds['h'] ?? []);
        if ($r > 0) break;
    }    

    if ($r === 0) {
        foreach(get_patterns($p['v'], array_map(function($n){return implode('', $n);}, $rotated)) as $codes) {
            $r = calculate($codes, 'v', $founds['v'] ?? []);
            if ($r > 0) break;
        }
    }

    $sum2 += $r;

}

echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;

function rotate($arr)
{
    $res = [];
    $pattern = array_map('str_split', $arr);

    for ($y = 0; $y < sizeof($pattern); $y++)
        for ($x = 0; $x < sizeof($pattern[0]); $x++)
            $res[$x][$y] = $pattern[$y][$x];
        
    return $res;
}

function calculate(array $codes, string $type, array $founds)
{
    $sum = 0;

    for ($i = 0; $i < sizeof($codes) - 1; $i++) {
        $slice1 = array_reverse(array_slice($codes, 0, $i + 1));
        $slice2 = array_slice($codes, $i + 1);
        $size = min(sizeof($slice1), sizeof($slice2));
        $slice1 = array_slice($slice1, 0, $size);
        $slice2 = array_slice($slice2, 0, $size);
        if ($slice1 === $slice2) {
            if (in_array($i + 1, $founds)) continue;
            
            $sum += ($i + 1) * (($type === 'h') ? 100 : 1);
            break;
        }
    }

    return $sum;
}

function get_patterns(array $p, $pattern)
{
    for($i = 0; $i < sizeof($p) - 1; $i++) {
        for($ii = $i + 1; $ii < sizeof($p); $ii++) {
            $x = $p[$i] ^ $p[$ii];

            if (($x !== 0) && (($x & ($x - 1)) === 0)) {
                $res = $p;
                $res[$i] = $res[$ii];
                yield $res;
            }
        }
    }
}