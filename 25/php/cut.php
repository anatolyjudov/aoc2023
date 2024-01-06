<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$nodes = [];

foreach ($inputLines as $line) {
    list($one, $many) = explode(': ', $line);
    $many = explode(' ', $many);

    foreach ($many as $node) {
        $nodes[$one][] = $node;
        $nodes[$node][] = $one;
    }
}

$dict = [];
$nodes_list = array_keys($nodes);
$sizeof = sizeof($nodes_list);

for($s = 0; $s < $sizeof; $s += rand(1, 200)) {
    for($k = 0; $k < $sizeof; $k += rand(1, 200)) {
        if ($s === $k) continue;

        echo (int)round(100 * ($s * $sizeof + $k) / $sizeof / $sizeof) . PHP_EOL;

        $paths = shortest_paths($nodes_list[$s], $nodes_list[$k], $nodes);

        for($p = 1; $p < sizeof($paths); $p++) {
            $key = min($paths[$p - 1], $paths[$p]) . max($paths[$p - 1], $paths[$p]);
            $dict[$key] = isset($dict[$key]) ? $dict[$key] + 1 : 1;
        }
    }
}

asort($dict);
print_r($dict);

$first = array_key_first($nodes);

test_without_edges(array_slice(array_keys($dict), -3), $first, $nodes);

function test_without_edges($edges, $first, &$nodes): void {

    $reverse_edges = [];
    foreach ($edges as $edge) {
        $reverse_edges[] = substr($edge,3) . substr($edge, 0, 3);
    }
    $edges = array_merge($edges, $reverse_edges);

    print_r($edges);

    $seen = [];
    check_next($first, $seen, $nodes, $edges);

    print_r($edges);
    echo sizeof($seen) . ", " . (sizeof($nodes) - sizeof($seen));
}

function check_next($next, &$seen, &$nodes, &$edges) {
    $seen[] = $next;

    foreach($nodes[$next] as $link) {
        if (in_array($next . $link, $edges)) {
            continue;
        }
        if (!in_array($link, $seen)) {
            check_next($link, $seen, $nodes,$edges);
        }
    }

    return $seen;
}


function shortest_paths($from, $end, &$nodes)
{
    $seen = [];

    get_next($from, $end, 0, [], $seen, $nodes);

    return $seen[$end]['p'];
}

function get_next($to, $end, $length, $path, &$seen, &$nodes)
{
    $seen[$to]['l'] = $length;
    $path[] = $to;
    if ($to === $end) {
        $seen[$to]['p'] = $path;
    }

    foreach($nodes[$to] as $link) {
        if ((!isset($seen[$link])) || ($seen[$link]['l'] > ($length + 1))) {
            get_next($link, $end, $length + 1, $path, $seen, $nodes);
        }
    }
}