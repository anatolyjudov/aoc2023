<?php

$inputLines = file('input.txt', FILE_IGNORE_NEW_LINES);

$workflows = [];
$parts = [];

$w = true;
foreach ($inputLines as $inputLine) {
    if ($w) {
        if ($inputLine === '') {
            $w = false;
            continue;
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
    } else {
        $part_str = explode(',', trim($inputLine, '{}'));
        $part = [];
        foreach($part_str as $stat) {
            list($k, $v) = explode('=', $stat);
            $part[$k] = $v;
        }
        $parts[] = $part;
    }
}

print_r($parts);
print_r($workflows);

function apply(array $part, string $workflow): string
{
    global $workflows;

    if (!isset($workflows[$workflow])) die ('Unknown workflow ' . $workflow);

    foreach($workflows[$workflow] as $rule) {
        if ($rule['type'] === 'result') {
            return $rule['value'];
        }
        if ($rule['type'] === 'redirect') {
            return apply($part, $rule['workflow']);
        }
        if ($rule['type'] === 'expr') {
            if (
                ($rule['op'] === '<' && ($part[$rule['stat']] < $rule['value'])) ||
                ($rule['op'] === '>' && ($part[$rule['stat']] > $rule['value']))
            ) {
                if ($rule['result'] === 'A' || $rule['result'] === 'R') {
                    return $rule['result'];
                }
                return apply($part, $rule['result']);
            }
            continue;
        }
        print_r($rule);
        die('Unknown rule');
    }
    print_r($part);
    echo $workflow;
    die('End of workflow');
}

$sum = 0;
foreach($parts as $part) {

    if (apply($part, 'in') === 'A') {
        $sum += array_sum($part);
    }
}

echo $sum;