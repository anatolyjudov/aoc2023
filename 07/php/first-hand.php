<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$games = [];

foreach ($inputLines as $game) {
    $hand = substr($game, 0, 5);
    $str = str_replace(["A","K","Q","J","T"], [">","=","<",";",":"], $hand);

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
        'str' => $str,
        'value' => $value
    ];
}

usort($games, function($a, $b) {
    return ($a['value'] !== $b['value']) ? $a['value'] <=> $b['value'] : $a['str'] <=> $b['str'];
});

$s = 0;
foreach($games as $r => $game) {
    $s += $game['bid'] * ($r + 1);
}

echo $s;