<?php

namespace App\Domain\Objects;

readonly class ImageDescription
{
    public function __construct(
        public ?string $value
    ) {}
}
