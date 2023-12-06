<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$time = str_replace(' ', '', substr($inputLines[0], 11));
$record = str_replace(' ', '', substr($inputLines[1], 11));

$d = $time ** 2 - 4 * $record;
$root1 = ($time - sqrt($d)) / 2;
$root2 = ($time + sqrt($d)) / 2;

$root1 = is_int($root1) ? $root1 + 1 : ceil($root1);
$root2 = is_int($root2) ? $root2 - 1 : floor($root2);

$options = $root2 - $root1 + 1;

echo $options . "\r\n";