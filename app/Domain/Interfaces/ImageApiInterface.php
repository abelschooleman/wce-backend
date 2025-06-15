<?php

namespace App\Domain\Interfaces;

use App\Domain\Objects\CityName;
use App\Domain\Objects\ImageCollection;

interface ImageApiInterface
{
    public function fetchImageCollection(CityName $city): ImageCollection;
}
