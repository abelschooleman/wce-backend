<?php

namespace App\Application\Geocode;

use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CityName;

class FindCityByName
{
    /**
     * @return City[]
     * */
    public function __invoke(WeatherApiInterface $api, CityName $cityName): array
    {
        return $api->findCityByName($cityName);
    }
}
