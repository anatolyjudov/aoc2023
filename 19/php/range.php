<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$workflows = [];

$w = true;
foreach ($inputLines as $inputLine) {

    if ($inputLine === '') {
        break;
    }
    $id = substr($inputLine, 0, strpos($inputLine, '{'));
    $rules_str = explode(',', trim(substr($inputLine, strlen($id) + 1), '}'));
    $rules = [];
    foreach($rules_str as $rule) {
        if ($rule === 'A' || $rule === 'R') {
            $rules[] = [
                'type' => 'result',
                'value' => $rule
            ];
            continue;
        }
        if (strpos($rule, ':') === false) {
            $rules[] = [
                'type' => 'redirect',
                'workflow' => $rule
            ];
            continue;
        }
        list($expression, $result) = explode(':', $rule);
        $stat = substr($expression, 0, 1);
        $operator = substr($expression, 1, 1);
        $value = substr($expression, 2);
        $rules[] = [
            'type' => 'expr',
            'stat' => $stat,
            'op' => $operator,
            'value' => $value,
            'result' => $result
        ];
    }
    $workflows[$id] = $rules;

}

print_r($parts);
print_r($workflows);

function apply(array $range, string $workflow): string
{
    global $workflows;

    echo 'Checking range: ' . PHP_EOL; print_r($range);

    if ($range === []) return 0;

    if (!isset($workflows[$workflow])) die ('Unknown workflow ' . $workflow);

    $sum = 0;
    foreach($workflows[$workflow] as $rule) {
        if ($range === []) break;

        if ($rule['type'] === 'result') {
            echo 'accepted' . PHP_EOL;
            if ($rule['value'] === 'A') {
                $sum += sum_range($range);
            }
            break;
        }
        if ($rule['type'] === 'redirect') {
            echo 'redir' . PHP_EOL;
            $sum += apply($range, $rule['workflow']);
            break;
        }
        if ($rule['type'] === 'expr') {
            $bottom = $range[$rule['stat']][0];
            $top = $range[$rule['stat']][1];

            echo '- expression' . PHP_EOL;
            print_r($rule);

            if ($rule['op'] === '<') {
                if ($rule['value'] <= $bottom) {
                    $left_range = [];
                    $right_range = $range;
                } elseif ($rule['value'] > $top) {
                    $left_range = $range;
                    $right_range = [];
                } else {
                    $left_range = $range;
                    $left_range[$rule['stat']] = [$bottom, $rule['value'] - 1];
                    $right_range = $range;
                    $right_range[$rule['stat']] = [$rule['value'], $top];
                }
            } elseif ($rule['op'] === '>') {
                if ($rule['value'] < $bottom) {
                    $left_range = [];
                    $right_range = $range;
                } elseif ($rule['value'] >= $top) {
                    $left_range = $range;
                    $right_range = [];
                } else {
                    $left_range = $range;
                    $left_range[$rule['stat']] = [$bottom, $rule['value']];
                    $right_range = $range;
                    $right_range[$rule['stat']] = [$rule['value'] + 1, $top];
                }
            }

            echo '- splitted into: ' . PHP_EOL;
            print_r($left_range);
            print_r($right_range);

            if ($rule['op'] === '>') {
                $range = $left_range;
                if ($rule['result'] === 'A') {
                    $sum += sum_range($right_range);
                    continue;
                }
                if ($rule['result'] === 'R') {
                    continue;
                }
                $sum += apply($right_range, $rule['result']);
                continue;
            }

            if ($rule['op'] === '<') {
                $range = $right_range;
                if ($rule['result'] === 'A') {
                    $sum += sum_range($left_range);
                    continue;
                }
                if ($rule['result'] === 'R') {
                    continue;
                }
                $sum += apply($left_range, $rule['result']);
                continue;
            }
        }
        print_r($rule);
        die('Unknown rule');
    }

    return $sum;
}

function sum_range(array $range): int
{
    $pr = 1;
    foreach($range as $stat_range) {
        $pr = $pr * ($stat_range[1] - $stat_range[0] + 1);       
    }
    return $pr;
}

$range = [
    'x' => [1, 4000],
    'm' => [1, 4000],
    'a' => [1, 4000],
    's' => [1, 4000]
];

$accepted = apply($range, 'in');

echo $accepted;