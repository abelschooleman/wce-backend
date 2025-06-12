<?php

namespace App\Infrastructure\OpenWeatherMap;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use JsonException;

class OpenWeatherMapResponse
{
    public int   $humidity;

    public float $temperature;

    public array $weather;

    /**
     * @throws JsonException
     * @throws OpenWeatherMapClientException
     */
    public function __construct(Response $response)
    {
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY);

        if (is_null($data)) {
            throw new OpenWeatherMapClientException('API did not return any data');
        }

        if (is_null($humidity = Arr::get($data, 'main.humidity'))) {
            throw new OpenWeatherMapClientException('Humidity not set');
        }

        if (is_null($temperature = Arr::get($data, 'main.temp'))) {
            throw new OpenWeatherMapClientException('Temperature not set');
        }

        if (is_null($weather = Arr::get($data, 'weather'))) {
            throw new OpenWeatherMapClientException('Weather not set');
        }

        $this->humidity = $humidity;
        $this->temperature = $temperature;
        $this->weather = $weather;
    }
}
