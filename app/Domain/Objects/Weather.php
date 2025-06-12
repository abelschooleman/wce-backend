<?php

namespace App\Domain\Objects;

class Weather
{
    public function __construct(
        public int $id,
        public string $main,
        public string $description,
    ) {}
}
