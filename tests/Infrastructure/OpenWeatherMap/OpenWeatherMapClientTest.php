<?php

namespace Tests\Infrastructure\OpenWeatherMap;

use App\Domain\Objects\City;
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new OpenWeatherMapClient();
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

        $this->client->fetchCurrentWeatherInCity(new City('TestCity'));

        Http::assertSent(fn (Request $request) => ['q' => 'TestCity', 'appid' => 'weather-api-key'] === $request->data());
    }

    /**
     * @throws JsonException
     */
    public function testOpenWeatherMapClientExceptionIsThrownWhenRequestFails(): void
    {
        Http::fake(fn (Request $request) => Http::response(['error' => 'Not authorized'], 401));

        $this->expectException(OpenWeatherMapClientException::class);
        $this->expectExceptionMessage('Request error when fetching data from OpenWeatherMap API: 401');

        $this->client->fetchCurrentWeatherInCity(new City('TestCity'));
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

        $this->client->fetchCurrentWeatherInCity(new City('TestCity'));
    }
}
