<?php

namespace App\Application\Weather;

use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CurrentCityWeather;

class FetchCurrentWeatherInCity
{
    public function __invoke(WeatherApiInterface $client, City $city): CurrentCityWeather
    {
        return $client->fetchCurrentWeatherInCity($city);
    }
}
