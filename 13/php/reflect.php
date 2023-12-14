<?php
/*
$a = bindec('0111111');
$b = bindec('0101111');

echo $a . ' ' . $b . PHP_EOL;

echo 'xor: ' . (int)($a ^ $b) . PHP_EOL;

die();
*/

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

    foreach ($pattern as $line) {
        $p['h'][] = bindec(str_replace(['.', '#'], ['0', '1'], $line));
    }

    $rotated = rotate($pattern);
    foreach ($rotated as $line) {
        $p['v'][] = bindec(str_replace(['.', '#'], ['0', '1'], implode('', $line)));
    }

    echo "Pattern #" . $pattern_num . PHP_EOL;

    print_r($pattern);

    /*
    for($i = 0; $i < sizeof($p['h']) - 1; $i++) {
        for($ii = $i + 1; $ii < sizeof($p['h']); $ii++) {
            $v1 = $p['h'][$i];
            $v2 = $p['h'][$ii];
            $x = abs($v2 - $v1);
            if ($x !== 0 && ($x & ($x - 1)) === 0) {
                echo "Found: $i and $ii" . PHP_EOL;
                echo $pattern[$i] . PHP_EOL . $pattern[$ii] . PHP_EOL;
            }
        }
    }
    */

    $founds = [];
    foreach ($p as $type => $codes) {
        for ($i = 0; $i < sizeof($codes) - 1; $i++) {
            $slice1 = array_reverse(array_slice($codes, 0, $i + 1));
            $slice2 = array_slice($codes, $i + 1);
            $size = min(sizeof($slice1), sizeof($slice2));
            $slice1 = array_slice($slice1, 0, $size);
            $slice2 = array_slice($slice2, 0, $size);
            if ($slice1 === $slice2) {
                $sum1 += ($i + 1) * (($type === 'h') ? 100 : 1);
                $founds[$type][] = $i + 1;
            }
        }
    }

    echo "part1 founds:" . PHP_EOL;
    print_r($founds);

    $r = 0;
    foreach(get_patterns($p['h'], $pattern) as $codes) {
        $r = calculate($codes, 'h', $founds['h'] ?? []);
        if ($r > 0) {
            echo "h+ " . $r . PHP_EOL;
            break;
        }
    }    

    if ($r === 0) {
        foreach(get_patterns($p['v'], array_map(function($n){return implode('', $n);}, $rotated)) as $codes) {
            $r = calculate($codes, 'v', $founds['v'] ?? []);
            if ($r > 0) {
                echo "v+ " . $r . PHP_EOL;
                break;
            }
        }
    }

    $sum2 += $r;

}

echo $sum1 . PHP_EOL . $sum2 . PHP_EOL;

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
            if (in_array($i + 1, $founds)) {
                echo 'Skipped found' . ($i + 1) . PHP_EOL;
                continue;
            }
            if ($sum != 0) {
                echo 'second reflection found ';
                print_r($codes);
            }
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
            $v1 = $p[$i];
            $v2 = $p[$ii];
            $x = $v2 ^ $v1;
            if (($x !== 0) && (($x & ($x - 1)) === 0)) {
                echo "Pattern fix: $i and $ii ($v2 ^ $v1 = $x)" . PHP_EOL;
                echo $pattern[$i] . PHP_EOL . $pattern[$ii] . PHP_EOL;
                $res = $p;
                $res[$i] = $res[$ii];
                yield $res;
            }
        }
    }
}

// 19119 is too low
// 30423 break after first matched pattern
// 62928 too high
// 30249 not right
// 19667