<?php

class ColorSet
{
    public function __construct(public int $red = 0, public int $green = 0, public int $blue = 0) {}

    public function fits(ColorSet $limitConfig): bool
    {
        return ($this->red <= $limitConfig->red)
            && ($this->green <= $limitConfig->green)
            && ($this->blue <= $limitConfig->blue);
    }

    public function merge(ColorSet $config): void
    {
        if ($this->red < $config->red) $this->red = $config->red;
        if ($this->green < $config->green) $this->green = $config->green;
        if ($this->blue < $config->blue) $this->blue = $config->blue;
    }

    public function power(): int
    {
        return $this->red * $this->blue * $this->green;
    }

    public static function createFromString(string $roundData): ColorSet
    {
        $red = $green = $blue = 0;

        foreach(explode(',', $roundData) as $ballInfo) {
            if (str_ends_with($ballInfo, 'd')) {
                $red = (int) substr($ballInfo, 0, -4);
            } elseif (str_ends_with($ballInfo, 'n')) {
                $green = (int) substr($ballInfo, 0, -6);
            } elseif (str_ends_with($ballInfo, 'e')) {
                $blue = (int) substr($ballInfo, 0, -5);
            }
        }

        return new ColorSet($red, $green, $blue);
    }
}