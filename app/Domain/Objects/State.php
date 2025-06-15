<?php

namespace App\Domain\Objects;

readonly class State
{
    public function  __construct(
        public string $value
    ) {}
}
