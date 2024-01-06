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
    global $modules, $state;

    foreach($modules as $id => $m) {

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

reset_state();

$feed = 'hb';

$pushes = 0;
$cycles = [];
while(true) {

    $pushes++;

    $pulses = [['broadcaster', LOW_PULSE, null]];
    $total = [LOW_PULSE => 0, HIGH_PULSE => 0];

    while(sizeof($pulses) > 0) {
        $pulse_data = array_shift($pulses);
        $id = $pulse_data[0];
        $pulse = $pulse_data[1];
        $from = $pulse_data[2];

        $total[$pulse] += 1;

        $output_pulse = false;

        if (($id === $feed) && ($pulse === HIGH_PULSE)) {
            echo $from . ' ';
            if (!isset($pulse_history[$from])) {
                $cycles[$from] = $pushes;

                print_r($cycles);

                if (sizeof($cycles) === 4) {
                    print_r($cycles);
                    $cycles = array_values($cycles);
                    echo (int)gmp_lcm($cycles[0], gmp_lcm($cycles[1], gmp_lcm($cycles[2], $cycles[3]))) . PHP_EOL;
                    die();
                }
            }
        }

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
        }
    }
}
