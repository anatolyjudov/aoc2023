<?php

use Phpml\Math\Matrix;

require_once __DIR__ . '/vendor/autoload.php';

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$hails = [];

foreach($inputLines as $inputLine) {
    list($pos_str, $speed_str) = explode(' @ ', $inputLine);
    $pt = explode(', ', $pos_str);
    $st = explode(', ', $speed_str);

    $hails[] = [
        'p' => ['x' => $pt[0], 'y' => $pt[1], 'z' => $pt[2]],
        's' => ['x' => $st[0], 'y' => $st[1], 'z' => $st[2]]
    ];
}

$result = solve($hails[0], $hails[1], $hails[2]);

$sum = (int)$result[0] + (int)$result[1] +  (int)$result[2];

echo $sum . PHP_EOL;

function solve($h1, $h2, $h3): array
{
    // points 1, 2, axes x, y
    $eq[0] = [
        [
             $h1['s']['y'] - $h2['s']['y'],
            -1 * ($h1['s']['x'] - $h2['s']['x']),
             0,
             $h2['p']['y'] - $h1['p']['y'],
            -1 * ($h2['p']['x'] - $h1['p']['x']),
             0
        ],
          $h1['s']['y'] * $h1['p']['x']
        - $h1['s']['x'] * $h1['p']['y']
        - $h2['s']['y'] * $h2['p']['x']
        + $h2['s']['x'] * $h2['p']['y']
    ];

    // points 1, 2, axes x, z
    $eq[1] = [
        [
            $h1['s']['z'] - $h2['s']['z'],
            0,
            -1 * ($h1['s']['x'] - $h2['s']['x']),
            $h2['p']['z'] - $h1['p']['z'],
            0,
            -1 * ($h2['p']['x'] - $h1['p']['x']),
        ],
        $h1['s']['z'] * $h1['p']['x']
        - $h1['s']['x'] * $h1['p']['z']
        - $h2['s']['z'] * $h2['p']['x']
        + $h2['s']['x'] * $h2['p']['z']
    ];

    // points 1, 2, axes y, z
    $eq[2] = [
        [
            0,
            $h1['s']['z'] - $h2['s']['z'],
            -1 * ($h1['s']['y'] - $h2['s']['y']),
            0,
            $h2['p']['z'] - $h1['p']['z'],
            -1 * ($h2['p']['y'] - $h1['p']['y']),
        ],
        $h1['s']['z'] * $h1['p']['y']
        - $h1['s']['y'] * $h1['p']['z']
        - $h2['s']['z'] * $h2['p']['y']
        + $h2['s']['y'] * $h2['p']['z']
    ];

    // points 1, 3, axes x, y
    $eq[3] = [
        [
            $h1['s']['y'] - $h3['s']['y'],
            -1 * ($h1['s']['x'] - $h3['s']['x']),
            0,
            $h3['p']['y'] - $h1['p']['y'],
            -1 * ($h3['p']['x'] - $h1['p']['x']),
            0
        ],
        $h1['s']['y'] * $h1['p']['x']
        - $h1['s']['x'] * $h1['p']['y']
        - $h3['s']['y'] * $h3['p']['x']
        + $h3['s']['x'] * $h3['p']['y']
    ];

    // points 1, 3, axes x, z
    $eq[4] = [
        [
            $h1['s']['z'] - $h3['s']['z'],
            0,
            -1 * ($h1['s']['x'] - $h3['s']['x']),
            $h3['p']['z'] - $h1['p']['z'],
            0,
            -1 * ($h3['p']['x'] - $h1['p']['x']),
        ],
        $h1['s']['z'] * $h1['p']['x']
        - $h1['s']['x'] * $h1['p']['z']
        - $h3['s']['z'] * $h3['p']['x']
        + $h3['s']['x'] * $h3['p']['z']
    ];

    // points 1, 3, axes y, z
    $eq[5] = [
        [
            0,
            $h1['s']['z'] - $h3['s']['z'],
            -1 * ($h1['s']['y'] - $h3['s']['y']),
            0,
            $h3['p']['z'] - $h1['p']['z'],
            -1 * ($h3['p']['y'] - $h1['p']['y']),
        ],
        $h1['s']['z'] * $h1['p']['y']
        - $h1['s']['y'] * $h1['p']['z']
        - $h3['s']['z'] * $h3['p']['y']
        + $h3['s']['y'] * $h3['p']['z']
    ];

    $A = new Matrix([
        $eq[0][0], $eq[1][0], $eq[2][0], $eq[3][0], $eq[4][0], $eq[5][0],
    ]);

    $B = new Matrix([
        [$eq[0][1]], [$eq[1][1]], [$eq[2][1]], [$eq[3][1]], [$eq[4][1]], [$eq[5][1]],
    ]);

    if ($A->getDeterminant() == 0) {
        die('determinant is zero');
    }

    $X = $A->inverse()->multiply($B)->getColumnValues(0);

    var_dump($X);

    return $X;
}