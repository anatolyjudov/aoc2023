<?php

$grid = file('input.txt', FILE_IGNORE_NEW_LINES);

$Y = sizeof($grid);
$X = strlen($grid[0]);

for ($y = 0; $y < $Y; $y++) {
    $grid[$y] = str_split($grid[$y]);
}

$start = [0, strpos(implode('', $grid[0]), '.')];
$end = [$Y - 1, strpos(implode('', $grid[$Y - 1]), '.')];

$nodes['start'] = $start;
$nodes['end'] = $end;

$seen[$start[0]][$start[1]] = 'start';
$seen[$end[0]][$end[1]] = 'end';

$edges = [];

proceed('start', 1, [$start[0] + 1, $start[1]]);

print_r($edges);

$seen['start'] = true;
$longest = get_longest(0, 'start', $seen);

echo 'Part 2: ' . $longest . PHP_EOL;

function get_longest($length, $current, $seen) {
    global $edges;

    //echo $current . ', ' . $length . PHP_EOL;

    if ($current === 'end') {
        return $length;
    }

    $max_length = 0;
    foreach ($edges[$current] as $next => $next_length) {
        if (isset($seen[$next])) continue;

        $new_seen = $seen;
        $new_seen[$next] = true;
        $try_length = get_longest($length + $next_length, $next, $new_seen);
        if ($max_length < $try_length) {
            $max_length = $try_length;
        }
    }


    return $max_length;
}

function proceed($last_node, $segment_length, $current) {
    global $nodes, $edges, $grid, $seen, $X, $Y;

    $y = $current[0];
    $x = $current[1];

    // are we at a known node?
    if (isset($seen[$y][$x])) {
        if (($seen[$y][$x] !== ($y . ',' . $x)) && ($seen[$y][$x] !== 'end')) {
            // this path was already covered, cancel this branch
            return;
        }
        $found_node = $seen[$y][$x];
        $edges[$last_node][$found_node] = $segment_length;
        $edges[$found_node][$last_node] = $segment_length;
        return;
    }

    // where we can go?
    $options = [];
    foreach ([[1, 0], [-1, 0], [0, 1], [0, -1]] as $step) {
        $yn = $y + $step[0];
        $xn = $x + $step[1];

        // is this the edge of the map?
        if ($yn < 0 || $yn === $Y || $xn < 0 || $xn === $X) {
            continue;
        }

        // is there a rock?
        if ($grid[$yn][$xn] === '#') continue;

        // did we come from here?
        if (isset($seen[$yn][$xn]) && ($seen[$yn][$xn] === $last_node)) continue;

        // ok, we can go this way
        $options[] = $step;
    }

    // we have no way
    if (count($options) === 0) {
        // there should not be anything like that, right?
        die('dead end');
    }

    // if we have only one way, it's just a path
    if (count($options) === 1) {
        $seen[$y][$x] = $last_node;
        $yn = $y + $options[0][0];
        $xn = $x + $options[0][1];
        proceed($last_node, $segment_length + 1, [$yn, $xn]);
        return;
    }

    // new node
    $node_key = $y . ',' . $x;
    $nodes[$node_key] = [$y, $x];
    $seen[$y][$x] = $node_key;
    $edges[$last_node][$node_key] = $segment_length;
    $edges[$node_key][$last_node] = $segment_length;

    foreach($options as $step) {
        $yn = $y + $step[0];
        $xn = $x + $step[1];
        proceed($node_key, 1, [$yn, $xn]);
    }
}

// 4998 too low