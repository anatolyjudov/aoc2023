<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

foreach([1, 2] as $part) {
    $start = [0, 0];
    $dots = [$start];
    $perimeter = 1;

    foreach($inputLines as $n => $line) {
        $data = explode(' ', $line);

        if ($part === 1) {
            $dir = $data[0];
            $dist = $data[1];
        } else {
            $dir = match(substr($data[2], -2, 1)) {'0' => 'R', '1' => 'D', '2' => 'L', '3' => 'U'};
            $dist = hexdec(substr($data[2], 2, -2));
        }
        
        $end[0] = $start[0] + match($dir) {'U' => -1, 'D' => 1, 'L', 'R' => 0} * $dist;
        $end[1] = $start[1] + match($dir) {'U', 'D' => 0, 'L' => -1, 'R' => 1} * $dist;

        $dots[] = $end;
        $perimeter += $dist;

        $start = $end;
    }

    $sum = 0;

    for($i = 1; $i < sizeof($dots); $i++) {
        $sum += $dots[$i - 1][0] * $dots[$i][1] - $dots[$i - 1][1] * $dots[$i][0];
    }

    $sum = abs($sum / 2) + ($perimeter + 1) / 2;

    echo "Part $part: $sum" . PHP_EOL;
}