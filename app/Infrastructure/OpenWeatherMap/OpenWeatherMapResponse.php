<?php

namespace App\Infrastructure\OpenWeatherMap;

use Illuminate\Http\Client\Response;
use JsonException;

class OpenWeatherMapResponse
{
    public array $data;

    /**
     * @throws JsonException
     * @throws OpenWeatherMapClientException
     */
    public function __construct(Response $response)
    {
        $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR | JSON_OBJECT_AS_ARRAY);

        if (is_null($data)) {
            throw new OpenWeatherMapClientException('API returned invalid data');
        }

        $this->data = $data;
    }
}
