<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$games = [];

foreach ($inputLines as $game) {
    $hand = str_replace(["A","K","Q","J","T"], [">","=","<",";",":"], substr($game, 0, 5));

    $cards = [];
    foreach (str_split($hand) as $card) {
        $cards[$card] = isset($cards[$card]) ? $cards[$card] + 1 : 1;
    }
    arsort($cards, SORT_NUMERIC);

    $value = match (current($cards)) {
        5 => 6,
        4 => 5,
        3 => match (next($cards)) {
            2 => 4,
            default => 3,
        },
        2 => match (next($cards)) {
            2 => 2,
            default => 1,
        },
        default => 0,
    };

    $games[] = [
        'bid' => substr($game, 6),
        'hand' => $hand,
        'value' => $value
    ];
}

usort($games, function($a, $b) {
    return ($a['value'] !== $b['value']) ? $a['value'] <=> $b['value'] : $a['hand'] <=> $b['hand'];
});

$s = 0;
foreach($games as $r => $game) {
    $s += $game['bid'] * ($r + 1);
}

echo $s;