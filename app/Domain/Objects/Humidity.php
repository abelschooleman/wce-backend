<?php

namespace App\Domain\Objects;

readonly class Humidity
{
    public function __construct(
        public int $value,
    ) {}
}
