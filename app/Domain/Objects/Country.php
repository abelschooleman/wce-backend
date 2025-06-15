<?php

namespace App\Domain\Objects;

readonly class Country
{
    public function __construct(
        public string $value,
    ) {}
}
