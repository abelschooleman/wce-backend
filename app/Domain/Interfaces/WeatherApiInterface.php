<?php

namespace App\Domain\Interfaces;

use App\Domain\Objects\City;
use App\Domain\Objects\CurrentCityWeather;

interface WeatherApiInterface
{
    public function fetchCurrentWeatherInCity(City $city): CurrentCityWeather;
}
