<?php

namespace App\Domain\Interfaces;

use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\CurrentCityWeather;

interface WeatherApiInterface
{
    /**
     * @return City[]
     */
    public function findCityByName(CityName $cityName): array;

    public function fetchCurrentWeatherInCity(City $city): CurrentCityWeather;
}
