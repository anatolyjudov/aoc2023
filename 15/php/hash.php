<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$strings = explode(',', $inputLines[0]);

$sum1 = $focusing_power = 0;
$boxes = [];

foreach($strings as $string) {
    $sum1 += aoc_hash($string);

    if (str_ends_with($string, '-')) {
        $box = substr($string, 0, -1);
        $hash = aoc_hash($box);
        
        if (isset($boxes[$hash][$box]))
            unset($boxes[$hash][$box]);

    } elseif (str_contains($string, '=')) {
        list($box, $length) = explode('=', $string);
        $boxes[aoc_hash($box)][$box] = $length;
    }
}

echo $sum1 . PHP_EOL;

foreach($boxes as $box_num => $box)
    foreach(array_values($box) as $slot => $lens_power) 
        $focusing_power += ($box_num + 1) * ($slot + 1) * $lens_power;

echo $focusing_power . PHP_EOL;

function aoc_hash(string $input): int
{
    $current = 0;

    foreach(str_split($input) as $c)
        $current = (($current + ord($c)) * 17) % 256;

    return $current;
}