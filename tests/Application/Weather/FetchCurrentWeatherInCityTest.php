<?php

namespace Tests\Application\Weather;

use App\Application\Weather\FetchCurrentWeatherInCity;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
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
        $city = new City('TestCity');

        /* @var WeatherApiInterface&MockObject $client */
        $client = $this->createMock(WeatherApiInterface::class);

        $client->expects($this->once())
            ->method('fetchCurrentWeatherInCity')
            ->with($city);

        new FetchCurrentWeatherInCity()($client, $city);
    }
}
