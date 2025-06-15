<?php

namespace Tests\Application\Http\Controllers;

use App\Application\Weather\FetchCurrentWeatherInCity;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\CurrentCityWeather;
use App\Domain\Objects\Humidity;
use App\Domain\Objects\Temperature;
use App\Domain\Objects\Weather;
use Mockery;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;

class GetCityWeatherControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCityWeatherIsFetchedAndReturned(): void
    {
        $apiResponse = new CurrentCityWeather(
            new Humidity(30),
            new Temperature(300),
            [new Weather(1, 'Test','Test weather')],
        );

        $this->instance(WeatherApiInterface::class, Mockery::mock(WeatherApiInterface::class, function ($mock) {
            $mock->shouldReceive('isAvailable')
                ->once()
                ->andReturn(true);
        }));

        $this->instance(FetchCurrentWeatherInCity::class, Mockery::mock(FetchCurrentWeatherInCity::class, function ($mock) use ($apiResponse) {
            $mock->shouldReceive('__invoke')
                ->once()
                ->andReturn($apiResponse);
        }));

        $this->getJson('api/weather?name=TestCity&country=TestCountry&state=TestState&latitude=33.23&longitude=3.4')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'temperature_in_celcius' => 26.85,
                    'humidity' => 30,
                    'weather' => [
                        [
                            'main' => 'Test',
                            'description' => 'Test weather',
                        ],
                    ],
                ],
            ]);
    }

    public function testBadRequestResponseIsReturnedWhenCityIsNotGiven(): void
    {
        $this->getJson('api/weather')->assertBadRequest();
    }

    public function testServiceUnavailableResponseIsReturnedWhenWeatherApiIsUnavailable(): void
    {
        $this->instance(WeatherApiInterface::class, Mockery::mock(WeatherApiInterface::class, function ($mock) {
            $mock->shouldReceive('isAvailable')
                ->once()
                ->andReturn(false);
        }));

        $this->getJson('api/weather?name=TestCity&country=TestCountry&state=TestState&latitude=33.23&longitude=3.4')
            ->assertServiceUnavailable();
    }
}
