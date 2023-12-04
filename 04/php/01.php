<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$cards = [];

foreach($inputLines as $inputLine) {
    $del1 = strpos($inputLine, ':');
    $del2 = strpos($inputLine, '|');
    $card['win'] = $card['played'] = [];

    foreach (explode(' ', trim(substr($inputLine, $del1 + 1, $del2 - $del1 - 1))) as $num) {
        if ($num === '') continue;

        $card['win'][] = (int) $num;
    }

    foreach (explode(' ', trim(substr($inputLine, $del2 + 1))) as $num) {
        if ($num === '') continue;

        $card['played'][] = (int) $num;
    }

    $card['ratio'] = sizeof(array_intersect($card['played'], $card['win']));
    $card['amount'] = 1;
    $cards[] = $card;
}

$sum = $sum2 = 0;

for($c = 0; $c < sizeof($cards); $c++) {
    $card = $cards[$c];

    if ($card['ratio'] > 0) {
        $sum += 1 << ($card['ratio'] - 1);
    }

    $amount = $card['amount'];
    $sum2 += $amount;

    for($i = 1; $i <= $card['ratio']; $i++) {
        $cards[$c + $i]['amount'] += $amount;
    }
}

echo $sum . "\r\n";
echo $sum2 . "\r\n";