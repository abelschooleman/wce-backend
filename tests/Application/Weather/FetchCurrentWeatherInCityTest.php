<?php

namespace Tests\Application\Weather;

use App\Application\Weather\FetchCurrentWeatherInCity;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Coordinates;
use App\Domain\Objects\Country;
use App\Domain\Objects\State;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FetchCurrentWeatherInCityTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCurrentWeatherInCityIsFetched(): void
    {
        $city = new City(new Coordinates(23.34, 1.23), new CityName('TestCity'), new Country('TestCountry'), new State('TestState'));

        /* @var WeatherApiInterface&MockObject $client */
        $client = $this->createMock(WeatherApiInterface::class);

        $client->expects($this->once())
            ->method('fetchCurrentWeatherInCity')
            ->with($city);

        new FetchCurrentWeatherInCity()($client, $city);
    }
}
