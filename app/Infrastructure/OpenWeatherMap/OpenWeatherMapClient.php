<?php

namespace App\Infrastructure\OpenWeatherMap;

use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CurrentCityWeather;
use App\Domain\Objects\Humidity;
use App\Domain\Objects\Temperature;
use App\Domain\Objects\Weather;
use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreaker;
use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreakerInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JsonException;

readonly class OpenWeatherMapClient implements WeatherApiInterface, CircuitBreakerInterface
{
    use CircuitBreaker;

    private const string API_BASE_URL = 'https://api.openweathermap.org/data/2.5/weather';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('weather.open_weather_map.api_key');
    }

    /**
     * @throws OpenWeatherMapClientException|JsonException
     */
    public function fetchCurrentWeatherInCity(City $city): CurrentCityWeather
    {
        $response = $this->fetch(new QueryParams($city->name));

        return new CurrentCityWeather(
            new Humidity($response->humidity),
            new Temperature($response->temperature),
            array_map(fn ($weather) => new Weather(...array_values($weather)), $response->weather),
        );
    }

    public function getId(): string
    {
        return get_class($this);
    }

    /**
     * @throws OpenWeatherMapClientException|JsonException
     */
    private function fetch(QueryParams $params): OpenWeatherMapResponse
    {
        $url = sprintf('%s?%s&appid=%s', self::API_BASE_URL, $params->toQueryString(), $this->apiKey);

        try {
            /* @var Response $response */
            $response = Http::throw()->get($url);

            return new OpenWeatherMapResponse($response);
        } catch (RequestException $requestException) {
            throw new OpenWeatherMapClientException(
                'Request error when fetching data from OpenWeatherMap API: ' . $requestException->getCode(),
                0,
                $requestException
            );
        } catch (ConnectionException $connectionException) {
            $this->setOpened();

            throw new OpenWeatherMapClientException('Could not connect to OpenWeatherMap API', previous: $connectionException);
        }
    }
}
