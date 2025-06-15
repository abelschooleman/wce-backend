<?php

namespace Tests\Application\Http\Controllers;

use App\Application\Geocode\FindCityByName;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Coordinates;
use App\Domain\Objects\Country;
use App\Domain\Objects\State;
use Mockery;
use Tests\TestCase;

class FindCityByNameControllerTest extends TestCase
{
    public function testReturnsCollectionOfCities(): void
    {
        $this->instance(WeatherApiInterface::class, Mockery::mock(WeatherApiInterface::class, function ($mock) {
            $mock->shouldReceive('isAvailable')
                ->once()
                ->andReturn(true);
        }));

        $this->instance(FindCityByName::class, Mockery::mock(FindCityByName::class, function ($mock) {
            $mock->expects('__invoke')
                ->once()
                ->andReturn([
                    new City(
                        new CityName('TestCity'),
                        new Country('TestCountry'),
                        new State('TestState'),
                        new Coordinates(23.234, 2.23),
                    ),
                ]);
        }));

        $this->getJson('api/find-city?query=TestCity')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'name' => 'TestCity',
                        'country' => 'TestCountry',
                        'state' => 'TestState',
                        'latitude' => 23.234,
                        'longitude' => 2.23,
                    ],
                ],
            ]);
    }
}
