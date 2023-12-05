<?php

class Maps
{
    protected array $maps = [];

    public function add($source, $destination, $d, $s, $l) {
        $this->maps[$source][$destination][] = [(int)$d, (int)$s, (int)$l - 1];

        usort(
            $this->maps[$source][$destination],
            function($a, $b) {
                return $a[1] <=> $b[1];
            }
        );
    }

    public function getDestinationFor(string $source): ?string
    {
        if (!isset($this->maps[$source])) return null;
        return key($this->maps[$source]);
    }

    public function getMapsFor(string $source): array
    {
        return $this->maps[$source];
    }

}