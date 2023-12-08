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

$ghostPaths = [];

foreach ($ghosts as $ghost => $start) {
    $current = $start;
    $step = 0;

    while($current[2] !== 'Z') {
        $current = $maps[$current][$lr[$step % sizeof($lr)]];
        $step++;
    }

    $ghostPaths[$ghost] = $step;
}

$factors = [];

foreach ($ghostPaths as $ghostSteps)
    foreach (factors($ghostSteps) as $f => $q)
        $factors[$f] = isset($factors[$f]) ? max($factors[$f], $q) : $q;

foreach ($factors as $factor => $q)
    $lcm = ($lcm ?? 1) * $factor * $q;

echo $lcm;

function factors(int $num): array
{
    $factors = [];
    $left = $num;
    do {
        $n = (int)gmp_nextprime($n ?? 1);

        while (($left % $n) === 0) {
            $factors[$n] = isset($factors[$n]) ? $factors[$n] + 1 : 1;
            $left = intdiv($left, $n);
        }
    } while ($left > 1 || $n < ($num / 2));

    return $factors;
}
