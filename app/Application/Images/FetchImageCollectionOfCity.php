<?php

namespace App\Application\Images;

use App\Domain\Interfaces\ImageApiInterface;
use App\Domain\Objects\CityName;
use App\Domain\Objects\ImageCollection;

class FetchImageCollectionOfCity
{
    public function __invoke(ImageApiInterface $imageApi, CityName $city): ImageCollection
    {
        return $imageApi->fetchImageCollection($city);
    }
}
