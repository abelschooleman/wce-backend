<?php

namespace App\Domain\Objects;

class Coordinates
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}
}
