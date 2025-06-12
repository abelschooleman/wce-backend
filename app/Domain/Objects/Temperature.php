<?php

namespace App\Domain\Objects;

readonly class Temperature
{
    /**
     * @param int $value Absolute temperature in Kelvin
     * */
    public function __construct(
        public int $value,
    ) {}

    public function toCelcius(): float
    {
        return round($this->value - 273.15, 2);
    }
}
