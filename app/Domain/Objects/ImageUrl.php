<?php

namespace App\Domain\Objects;

readonly class ImageUrl
{
    public function __construct(
        public string $value
    ) {}
}
