<?php

include "ColorSet.php";

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);
$limitConfig = new ColorSet(12, 13, 14);

$sum = $power = 0;

foreach ($inputLines as $gameData) {
    $game = [
        'fits' => true,
        'id' => substr($gameData, 4, strpos($gameData,':') - 4),
        'maxConfig' => new ColorSet(0, 0, 0)
    ];

    $roundsData = substr($gameData, strlen($game['id']) + 6);

    foreach (explode(';', $roundsData) as $roundData) {
        $roundConfig = ColorSet::createFromString($roundData);
        $game['maxConfig']->merge($roundConfig);

        if (!$roundConfig->fits($limitConfig)) {
            $game['fits'] = false;
        }
    }

    $sum += $game['fits'] ? (int) $game['id'] : 0;
    $power += $game['maxConfig']->power();
}

echo $sum . "\r\n";
echo $power . "\r\n";