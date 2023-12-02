<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$symbols = [
    "1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "6" => "6", "7" => "7", "8" => "8", "9" => "9",
    "one" => "1", "two" => "2", "three" => "3", "four" => "4", "five" => "5", "six" => "6", "seven" => "7", "eight" => "8", "nine" => "9",
];

$calibrationValues = [];

foreach ($inputLines as $inputLine) {
    $digits = '';
    $line = $inputLine;

    while ($line !== '') {
        foreach($symbols as $symbol => $digit) {
            if (str_starts_with($line, $symbol)) {
                $digits .= $digit;
                break 2;
            }
        }
        $line = substr($line, 1);
    }

    while ($inputLine !== '') {
        foreach($symbols as $symbol => $digit) {
            if (str_ends_with($inputLine, $symbol)) {
                $digits .= $digit;
                break 2;
            }
        }
        $inputLine = substr($inputLine, 0, -1);
    }

    $calibrationValues[] = (int) ($digits);
}

echo array_sum($calibrationValues);
