<?php

const LOW_PULSE = 0;
const HIGH_PULSE = 1;

const ON = 1;
const OFF = 0;

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$modules = $state = $pulse_history = [];

// read
foreach($inputLines as $line) {
    if ($line === '') continue;

    if ($line[0] === '%' || $line[0] === '&') {
        $type = $line[0];
        list($id, $destinations_str) = explode(' -> ', substr($line, 1));
        $destinations = explode(', ', $destinations_str);
        $modules[$id]['type'] = $type;
        $modules[$id]['dests'] = $destinations;
        foreach($destinations as $dest) {
            $modules[$dest]['inputs'][] = $id;
        }
    } else if (str_starts_with($line, 'broadcaster')) {
        $modules['broadcaster'] = [
            'type' => 'b',
            'dests' => explode(', ', substr($line, 15))
        ];
    }
}

function reset_state() {
    global $modules, $state, $pulse_history;

    foreach($modules as $id => $m) {
        $pulse_history[$id] = [];

        if (!isset($m['type'])) {
            continue;
        }

        if ($m['type'] === '%') {
            $state[$id] = OFF;
            continue;
        }

        if ($m['type'] === '&') {
            foreach($m['inputs'] as $input) {
                $state[$id][$input] = LOW_PULSE; // low pulse
            }
        }
    }
}

function push($current_push): array {
    global $state, $modules, $pulse_history;

    $pulses = [['broadcaster', LOW_PULSE, null]];
    $total = [LOW_PULSE => 0, HIGH_PULSE => 0];

    while(sizeof($pulses) > 0) {
        $pulse_data = array_shift($pulses);
        $id = $pulse_data[0];
        $pulse = $pulse_data[1];
        $from = $pulse_data[2];

        $total[$pulse] += 1;

        $output_pulse = false;

        if (!isset($modules[$id]['type'])) {
            // untyped module
            continue;
        }

        if ($modules[$id]['type'] === '%') {
            if ($pulse === LOW_PULSE) {
                $state[$id] = $state[$id] ^ 1;
                $output_pulse = ($state[$id] === ON) ? HIGH_PULSE : LOW_PULSE;
            }
        }

        if ($modules[$id]['type'] === '&') {
            $state[$id][$from] = $pulse;
            $output_pulse = (array_product($state[$id]) === 1) ? LOW_PULSE : HIGH_PULSE;
        }

        if ($modules[$id]['type'] === 'b') {
            $output_pulse = $pulse;
        }

        if ($output_pulse !== false) {
            foreach($modules[$id]['dests'] as $dest) {
                array_push($pulses, [$dest, $output_pulse, $id]);
            }

            $pulse_history[$id][$current_push] = $output_pulse;
        }
    }

    return $total;
}

reset_state();
$total = [LOW_PULSE => 0, HIGH_PULSE => 0];
for($i = 0; $i < 1000; $i++) {
    $pulses = push($i);
    $total[LOW_PULSE] += $pulses[LOW_PULSE];
    $total[HIGH_PULSE] += $pulses[HIGH_PULSE];
}
echo "Part 1: " . $total[LOW_PULSE] * $total[HIGH_PULSE] . PHP_EOL;

foreach($modules as $id => $m) {
    if (!isset($m['type'])) continue;
    echo 'Module ' . $m['type'] . $id . PHP_EOL;
    foreach($pulse_history[$id] as $push => $pulse) echo "$push: $pulse" . PHP_EOL;
    echo PHP_EOL;
}
//print_r($pulse_history);

/*
print_r($modules['hb']);
print_r($state['hb']);
*/

/*
    [js] => 0
    [zb] => 0
    [bs] => 0
    [rr
*/

/*
$pushes = 0;
while($pushes < 10) {
    push();

    print_r($state['hb']);

    $pushes++;
}

echo $pushes;
*/
//print_r($state);