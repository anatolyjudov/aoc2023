<?php

include "Maps.php";
include "Recipe.php";

function input(string $filename): array {
    $inputLines = file($filename, FILE_IGNORE_NEW_LINES);

    $seeds = explode(' ', substr($inputLines[0], 7));

    $l = 2;
    $maps = new Maps();
    while($l < sizeof($inputLines)) {
        list($source, $destination) = explode('-to-', substr($inputLines[$l], 0, -5));

        $l++;

        while(!empty($inputLines[$l])) {
            $data = explode(' ', $inputLines[$l]);
            $maps->add($source, $destination, $data[0], $data[1], $data[2]);

            $l++;
        }

        $l++;
    }

    return [$seeds, $maps];
}

list($seeds, $maps) = input('input.txt');

$recipe = new Recipe($maps);
foreach ($seeds as $seed) {
    $recipe->resolveFromSe($seed);

    $location = $recipe->get('location');
    if (!isset($lowest) || $location < $lowest) {
        $lowest = $location;
    }
}

echo 'Part 1: ' . $lowest . "\r\n";

$t0 = hrtime(true);
$lowest = false;

for($s = 0; $s < sizeof($seeds); $s += 2) {
    $shift = 0;

    while($shift < $seeds[$s + 1]) {
        $skip = $recipe->resolveFromSe($seeds[$s] + $shift, $seeds[$s + 1]);

        if ($lowest === false || $recipe->get('location') < $lowest) {
            $lowest = $recipe->get('location');
        }

        $shift += ($skip + 1);
    }
}

echo 'Part 1: ' . $lowest . ', time: ' . (hrtime(true) - $t0) . " ns\r\n";