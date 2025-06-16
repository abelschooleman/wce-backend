<?php

namespace App\Infrastructure\OpenWeatherMap;

use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Coordinates;
use App\Domain\Objects\Country;
use App\Domain\Objects\CurrentCityWeather;
use App\Domain\Objects\Humidity;
use App\Domain\Objects\State;
use App\Domain\Objects\Temperature;
use App\Domain\Objects\Weather;
use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreaker;
use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreakerInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use JsonException;

readonly class OpenWeatherMapClient implements WeatherApiInterface, CircuitBreakerInterface
{
    use CircuitBreaker;

    private const string API_BASE_URL = 'https://api.openweathermap.org';

    private const string ICON_BASE_URL = 'https://openweathermap.org/img/wn';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('weather.open_weather_map.api_key');
    }

    /**
     * @throws OpenWeatherMapClientException
     * @throws JsonException
     *
     * @return City[]
     */
    public function findCityByName(CityName $cityName): array
    {
        $response = $this->fetch('geo/1.0/direct', new Query(['q' => $cityName->value, 'limit' => 10]));

        return array_map(function (array $city) {
            return new City(
                new Coordinates($city['lat'], $city['lon']),
                new CityName($city['name']),
                new Country($city['country']),
                isset($city['state']) ? new State($city['state']) : null,
            );
        }, $response->data);
    }

    /**
     * @throws OpenWeatherMapClientException|JsonException
     */
    public function fetchCurrentWeatherInCity(City $city): CurrentCityWeather
    {
        $response = $this->fetch('data/2.5/weather', new Query(['lat' => $city->coordinates->latitude, 'lon' => $city->coordinates->longitude]));

        if (is_null($humidity = Arr::get($response->data, 'main.humidity'))) {
            throw new OpenWeatherMapClientException('Humidity not set');
        }

        if (is_null($temperature = Arr::get($response->data, 'main.temp'))) {
            throw new OpenWeatherMapClientException('Temperature not set');
        }

        if (is_null($weather = Arr::get($response->data, 'weather'))) {
            throw new OpenWeatherMapClientException('Weather not set');
        }

        return new CurrentCityWeather(
            new Humidity($humidity),
            new Temperature($temperature),
            array_map(fn ($weather) => new Weather($weather['id'], $this->getIconUrl($weather['icon']), $weather['main'], $weather['description']), $weather),
        );
    }

    public function getId(): string
    {
        return get_class($this);
    }

    /**
     * @throws OpenWeatherMapClientException|JsonException
     */
    private function fetch(string $endpoint, Query $params): OpenWeatherMapResponse
    {
        $url = sprintf('%s/%s?%s&appid=%s', self::API_BASE_URL, $endpoint, $params->toQueryString(), $this->apiKey);

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

    private function getIconUrl(string $icon): string
    {
        return sprintf('%s/%s@2x.png', self::ICON_BASE_URL, $icon);
    }
}
