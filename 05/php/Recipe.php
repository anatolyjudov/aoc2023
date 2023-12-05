<?php

class Recipe
{
    protected array $values;

    public function __construct(protected Maps $maps) {}

    public function get(string $type)
    {
        return $this->values[$type];
    }

    public function resolveFromSe(int $se, $skippable = 1): int
    {
        $source = 'seed';
        $this->values[$source] = $se;

        do {
            $maps = $this->maps->getMapsFor($source);

            foreach ($maps as $destination => $rules) {

                foreach($rules as $rule) {
                    $sourceValue = $this->values[$source];

                    if ($sourceValue < $rule[1]) {
                        $this->values[$destination] = $sourceValue;

                    } elseif ($sourceValue <= ($rule[1] + $rule[2])) {
                        $this->values[$destination] = $rule[0] + ($sourceValue - $rule[1]);

                        $newSkippable = ($rule[0] + $rule[2]) - $this->values[$destination];
                        if ($skippable > $newSkippable) {
                            $skippable = $newSkippable;
                        }

                    } else {
                        continue;
                    }

                    // if we found an interval, this is the end of the loop over a map
                    break 2;
                }

                // nothing has been found in maps, it seems that the value is higher than any interval
                $this->values[$destination] = $this->values[$source];

                // not changing skippable
            }

            $source = $destination;
        } while ($this->maps->getDestinationFor($source) !== null);

        // if skippable wasn't defined, then nothing to skip
        return ($skippable === -1) ? 0 : $skippable;
    }
}