<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$numbers = $symbols = $gears = [];
$sum = $ratiosSum = 0;

for ($y = 0; $y < sizeof($inputLines); $y++) {
    $inputLine = $inputLines[$y] . '.';
    $number = '';

    for($x = 0; $x < strlen($inputLine); $x++) {
        $next = $inputLine[$x];

        if (ord($next) >= 48 && ord($next) <= 57) {
            $number .= $next;
            continue;
        }

        if ($number !== '') {
            $numbers[] = [
                'value' => $number,
                'y' => $y,
                'x1' => $x - strlen($number),
                'x2' => $x - 1
            ];
            $number = '';
        }

        if ($next !== '.') {
            $symbols[$y][$x] = $next;
        }
    }
}

foreach ($numbers as $n => $number) {
    $symbolFound = false;

    for($y = $number['y'] - 1; $y <= $number['y'] + 1; $y++) {

        for($x = $number['x1'] - 1; $x <= $number['x2'] + 1; $x++) {

            if (isset($symbols[$y][$x])) {
                $symbolFound = true;

                if ($symbols[$y][$x] === '*' && !isset($gears["$y:$x"][$n])) {
                    $gears["$y:$x"][$n] = $number['value'];
                }
            }
        }
    }

    $sum += $symbolFound ? $number['value'] : 0;
}

foreach ($gears as $gear) {
    if (sizeof($gear) === 2) {
        $ratiosSum += array_product($gear);
    }
}

print_r($symbols);
print_r($numbers);
print_r($gears);

echo $sum . "\r\n";
echo $ratiosSum . "\r\n";