<?php

namespace App\Domain\Objects;

readonly class CityName
{
    public function  __construct(
        public string $value
    ) {}
}
