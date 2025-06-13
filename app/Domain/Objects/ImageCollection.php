<?php

namespace App\Domain\Objects;

use ArrayIterator;
use Traversable;

class ImageCollection implements \IteratorAggregate
{
    /**
     * @var Image[]
     */
    public array $images;

    /**
     * @param Image[] $images
     */
    public function __construct(array $images)
    {
        $this->images = $images;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->images);
    }
}
