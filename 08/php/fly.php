<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$lr = str_split($inputLines[0]);

$maps = $ghosts = [];

for($i = 2; $i < sizeof($inputLines); $i++) {
    $ghost = substr($inputLines[$i], 0, 2);
    $key = substr($inputLines[$i], 0, 3);
    $maps[$key] = [
        'L' => substr($inputLines[$i], 7, 3),
        'R' => substr($inputLines[$i], 12, 3)
    ];
    if ($key[2] === 'A')
        $ghosts[$ghost] = $key;
}

foreach ($ghosts as $ghost => $current) {
    $step = 0;

    while($current[2] !== 'Z') {
        $current = $maps[$current][$lr[$step % sizeof($lr)]];
        $step++;
    }

    $lcm = isset($lcm) ? gmp_lcm($lcm, $step) : $step;
}

echo $lcm;