<?php

namespace App\Domain\Objects;

readonly class Image
{
    public function __construct(
        public ImageUrl $raw,
        public ImageUrl $full,
        public ImageUrl $regular,
        public ImageUrl $small,
        public ImageUrl $thumbnail,
        public ImageDescription $description,
    ) {}
}
