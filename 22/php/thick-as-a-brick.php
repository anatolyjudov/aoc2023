<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$bricks = [];

$map = [];

$X = $Y = 0;
$Z = 1;

$n = 0;
foreach ($inputLines as $line) {
    list($start, $end) = explode('~', $line);
    $brick = [
        array_map('intval', explode(',', $start)),
        array_map('intval', explode(',', $end))
    ];
    $bricks[$n] = $brick;
    $X = max($X, $brick[0][0], $brick[1][0]);
    $Y = max($Y, $brick[0][1], $brick[1][1]);
    $Z = max($Z, $brick[0][2], $brick[1][2]);

    set_map($brick, $n);

    $n++;
}

// FALL

uasort($bricks, function($a, $b) {
    return min($a[0][2], $a[1][2]) <=> min($b[0][2], $b[1][2]);
});

$supported = [];
$supports = [];

foreach($bricks as $b_num => $brick) {
    // keep falling down until break
    while (true) {
        assert(($brick[0][2] > 0) && ($brick[1][2] > 0), 'negative z');
        // touching the ground?
        if (($brick[0][2] === 1) || ($brick[1][2] === 1)) {
            continue 2;
        }

        $checks = [];
        if ($brick[0][2] === $brick[1][2]) {
            // horizontal
            $z = $brick[1][2];
            for($x = min($brick[0][0], $brick[1][0]); $x <= max($brick[0][0], $brick[1][0]); $x++) {
                for ($y = min($brick[0][1], $brick[1][1]); $y <= max($brick[0][1], $brick[1][1]); $y++) {
                    $checks[] = [$x, $y, $z - 1];
                }
            }
        } else {
            // vertical
            if ($brick[0][2] < $brick[1][2]) {
                $checks[] = [$brick[0][0], $brick[0][1], $brick[0][2] - 1];
            } else {
                $checks[] = [$brick[1][0], $brick[1][1], $brick[1][2] - 1];
            }
        }

        // is there any support?
        $has_support = [];
        foreach($checks as $check) {
            $try = $map[$check[2]][$check[0]][$check[1]] ?? false;
            if ($try !== false) {
                $has_support[] = $try;
                $supported[$b_num][] = $try;
                $supports[$try][] = $b_num;
            }
        }
        if (sizeof($has_support) > 0) {
            // brick is steady, go to the next one
            break;
        }

        // fall down
        remove_map($brick);
        $brick[0][2] = $brick[0][2] - 1;
        $brick[1][2] = $brick[1][2] - 1;
        set_map($brick, $b_num);
        $bricks[$b_num] = $brick;

        // the end of the loop
        // we return back and will try to fall again
    }

    // that's it, now take the next brick
}

$desintegrate = array_flip(array_keys($bricks));

foreach($supported as $b_num => $supported_by) {
    $supported_by = array_unique($supported_by);
    if (sizeof($supported_by) === 1) {
        if (isset($desintegrate[$supported_by[0]])) {
            //echo $supported_by[0] . ' cannot be desintegrated' . PHP_EOL;
            unset($desintegrate[$supported_by[0]]);
        }
    }
}

echo "Part 1: " . sizeof($desintegrate) . PHP_EOL;

uasort($bricks, function($b, $a) {
    return min($a[0][2], $a[1][2]) <=> min($b[0][2], $b[1][2]);
});

$sum = 0;
foreach($bricks as $brick_num => $brick) {
    //echo '---' . PHP_EOL . 'Test ' . $brick_num . PHP_EOL;
    $fall = test_fall($brick_num, $supported);
    $sum += $fall - 1;
}

echo "Part 2: " . $sum . PHP_EOL;

function test_fall($brick_num, $supported) {
    $fell = [$brick_num];

    while(true) {
        foreach ($supported as $b_num => $supported_by_nums) {
            $supported[$b_num] = array_diff($supported_by_nums, $fell);
        }

        $new_fell = false;
        foreach($supported as $b_num => $supported_by_nums) {
            if ($supported_by_nums === []) {
                $new_fell = true;
                $fell[] = $b_num;
                unset($supported[$b_num]);
            }
        }

        if (!$new_fell) break;
    }

    return sizeof($fell);
}

function set_map($brick, $brick_num) {
    global $map;

    for($x = min($brick[0][0], $brick[1][0]); $x <= max($brick[0][0], $brick[1][0]); $x++) {
        for($y = min($brick[0][1], $brick[1][1]); $y <= max($brick[0][1], $brick[1][1]); $y++) {
            for($z = min($brick[0][2], $brick[1][2]); $z <= max($brick[0][2], $brick[1][2]); $z++) {
                $map[$z][$x][$y] = $brick_num;
            }
        }
    }
}

function remove_map($brick) {
    global $map;

    for($x = min($brick[0][0], $brick[1][0]); $x <= max($brick[0][0], $brick[1][0]); $x++) {
        for($y = min($brick[0][1], $brick[1][1]); $y <= max($brick[0][1], $brick[1][1]); $y++) {
            for($z = min($brick[0][2], $brick[1][2]); $z <= max($brick[0][2], $brick[1][2]); $z++) {
                assert(isset($map[$z][$x][$y]), 'trying to remove from empty map cell');
                unset($map[$z][$x][$y]);
            }
        }
    }
}