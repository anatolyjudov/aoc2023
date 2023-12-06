<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$times = preg_split('/\s+/', trim(substr($inputLines[0], 11)));
$records = preg_split('/\s+/', trim(substr($inputLines[1], 11)));

$mul = 1;
for($race = 0; $race < sizeof($times); $race++) {

    $d = $times[$race] ** 2 - 4 * $records[$race];
    $root1 = ($times[$race] - sqrt($d)) / 2;
    $root2 = ($times[$race] + sqrt($d)) / 2;

    $root1 = is_int($root1) ? $root1 + 1 : ceil($root1);
    $root2 = is_int($root2) ? $root2 - 1 : floor($root2);

    $mul = $mul * ($root2 - $root1 + 1);
}

echo $mul . "\r\n";