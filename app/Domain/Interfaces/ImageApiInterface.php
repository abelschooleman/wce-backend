<?php

namespace App\Domain\Interfaces;

use App\Domain\Objects\City;
use App\Domain\Objects\ImageCollection;

interface ImageApiInterface
{
    public function fetchImageCollection(City $city): ImageCollection;
}
