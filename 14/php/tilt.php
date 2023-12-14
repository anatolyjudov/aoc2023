<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$grid = [];

foreach($inputLines as $line) {
    $grid[] = str_split($line);
}

$grid1 = rotate($grid);
$grid1 = tilt($grid1);
$sum = north_load($grid1);
show_grid($grid1);
echo "Part 1: $sum" . PHP_EOL . PHP_EOL;

$cycle = 0;
do {

    for($r = 0; $r < 4; $r++) {
        $grid = rotate($grid);
        $grid = tilt($grid);
    }

    $cycle++;

    //echo 'after cycle ' . $cycle . ' load is ' . north_load2($grid) . PHP_EOL; 
    //show_grid($grid);
    //echo north_load2($grid) . PHP_EOL;
    
    
    if ($cycle % 22 === 1000000000 % 22) {
        $sum = north_load2($grid);
        echo "Cycle: $cycle, load: $sum" . PHP_EOL;
    }
    
    
} while ($cycle < 1000);

function rotate(array &$grid): array {
    return array_map(function($c) use ($grid) {return array_column($grid, $c);}, array_keys($grid));
}

function tilt(array &$grid)
{
    $new_grid = [];
    $sum = 0;
    foreach($grid as $u => $line) {
        
        $line = implode('', $line);
    
        $last_hash = -1;
        $rolls_in_block = 0;
        $new_line = '';
        for($i = 0; $i <= strlen($line); $i++) {

            if (($i === strlen($line)) || ($line[$i] === '#')) {
                $subline = substr($line, $last_hash + 1, $i - $last_hash - 1);
            
                $new_line .= str_pad(str_repeat('O', $rolls_in_block), strlen($subline), '.', STR_PAD_RIGHT);
                if ($i < strlen($line)) $new_line .= '#';

                $last_hash = $i;
                $rolls_in_block = 0;
                
                continue;
            }
    
            if ($line[$i] === 'O') {
                $rolls_in_block++;
            }
        }

        $new_grid[] = str_split(strrev($new_line));
    }

    return $new_grid;
}

function show_grid(array &$grid): void
{
    //print_r($grid);
    echo implode("\r\n", array_map(function($l) {return implode('', $l);}, $grid)) . PHP_EOL . PHP_EOL;
}

function north_load(array &$grid): int
{
    $sum = 0;
    $h = sizeof($grid[0]);
    for($y = 0; $y < sizeof($grid); $y++) {
        for($x = 0; $x < $h; $x++) {
            if ($grid[$y][$x] === 'O') {
                $sum += $x + 1;
            }
        }
    }
    return $sum;
}

function north_load2(array &$grid): int
{
    $sum = 0;
    $h = sizeof($grid);
    for($y = 0; $y < $h; $y++) {
        for($x = 0; $x < sizeof($grid[$y]); $x++) {
            if ($grid[$y][$x] === 'O') {
                $sum += $h - $y;
            }
        }
    }
    return $sum;
}