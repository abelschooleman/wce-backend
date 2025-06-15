<?php

namespace Tests\Infrastructure\OpenWeatherMap;

use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Coordinates;
use App\Domain\Objects\Country;
use App\Domain\Objects\State;
use App\Infrastructure\OpenWeatherMap\OpenWeatherMapClient;
use App\Infrastructure\OpenWeatherMap\OpenWeatherMapClientException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use JsonException;
use Tests\TestCase;

class OpenWeatherMapClientTest extends TestCase
{
    protected OpenWeatherMapClient $client;

    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new OpenWeatherMapClient();

        $this->city = new City(
            new CityName('TestCity'),
            new Country('TCountry'),
            new State('TState'),
            new Coordinates(23.34, 1.23),
        );
    }

    /**
     * @throws OpenWeatherMapClientException
     * @throws JsonException
     */
    public function testQueryForCitiesThatMatchNameIsSent(): void
    {
        Http::fake(
            fn () => Http::response([
                [
                    'name' => 'TestCity1',
                    'lat' => 23.324,
                    'lon' => -2.234,
                    'country' => 'US',
                    'state' => 'CA',
                ],
                [
                    'name' => 'TestCity2',
                    'lat' => 34.324,
                    'lon' => 45.234,
                    'country' => 'BR',
                    'state' => 'DF',
                ],
            ])
        );

        $this->client->findCityByName(new CityName('TestC'));

        Http::assertSent(function (Request $request) {
            return ['q' => 'TestC', 'limit' => '10', 'appid' => 'weather-api-key'] === $request->data();
        });
    }

    /**
     * @throws OpenWeatherMapClientException|JsonException
     */
    public function testCurrentWeatherOfCityIsFetched(): void
    {
        Http::fake(
            fn () => Http::response([
                'main' => [
                    'temp' => 300,
                    'humidity' => 30,
                ],
                'weather' => [
                    [
                        'id' => 1,
                        'main' => 'Sunny',
                        'description' => 'Clear skies',
                        'icon' => '03d',
                    ],
                ],
            ])
        );

        $this->client->fetchCurrentWeatherInCity($this->city);

        Http::assertSent(function (Request $request) {
            return ['lat' => '23.34', 'lon' => '1.23', 'appid' => 'weather-api-key'] === $request->data();
        });
    }

    /**
     * @throws JsonException
     */
    public function testOpenWeatherMapClientExceptionIsThrownWhenRequestFails(): void
    {
        Http::fake(fn (Request $request) => Http::response(['error' => 'Not authorized'], 401));

        $this->expectException(OpenWeatherMapClientException::class);
        $this->expectExceptionMessage('Request error when fetching data from OpenWeatherMap API: 401');

        $this->client->fetchCurrentWeatherInCity($this->city);
    }

    /**
     * @throws JsonException
     */
    public function testOpenWeatherMapClientExceptionIsThrownWhenConnectionFailsAndCircuitBreakerIsOpened(): void
    {
        Http::fake(fn (Request $request) => Http::failedConnection());

        $this->expectException(OpenWeatherMapClientException::class);
        $this->expectExceptionMessage('Could not connect to OpenWeatherMap API');

        Redis::expects('set')
            ->once()
            ->withSomeOfArgs('cb:app_infrastructure_openweathermap_openweathermapclient');

        $this->client->fetchCurrentWeatherInCity($this->city);
    }
}
