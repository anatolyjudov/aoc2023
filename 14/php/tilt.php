<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$grid = [];

foreach($inputLines as $line) {
    $grid[] = str_split($line);
}

show_grid($grid);

$grid = rotate($grid);
list($sum, $grid) = tilt($grid);
echo $sum . PHP_EOL;

show_grid($grid);

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
            
                $top_lever = strlen($line) - $last_hash - 1;    
                $value = 0;
                for($r = 0; $r < $rolls_in_block; $r++) {
                    $value += $top_lever - $r;
                }
                $sum += $value;

                $new_line .= str_pad(str_repeat('O', $rolls_in_block), strlen($subline), '.', STR_PAD_RIGHT);
                if ($i < strlen($line)) $new_line .= '#';

                $last_hash = $i;
                $rolls_in_block = 0;
                
                continue;
            }
    
            if ($line[$i] === 'O') {
                $rolls_in_block++;
            }

            //$new_line .= '.';
        }

        $new_grid[$u] = str_split($new_line);
    }

    return [$sum, $new_grid];
}

function show_grid(array &$grid): void
{
    //print_r($grid);
    echo implode("\r\n", array_map(function($l) {return implode('', $l);}, $grid)) . PHP_EOL . PHP_EOL;
}