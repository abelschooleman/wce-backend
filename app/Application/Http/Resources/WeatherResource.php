<?php

namespace App\Application\Http\Resources;

use App\Domain\Objects\CurrentCityWeather;
use App\Domain\Objects\Weather;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    public function __construct(CurrentCityWeather $resource)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'temperature_in_celcius' => $this->resource->temperature->toCelcius(),
            'humidity' => $this->resource->humidity->value,
            'weather' => array_map(function (Weather $weather) {
                return [
                    'main' =>  $weather->main,
                    'description' => $weather->description,
                ];
            }, $this->resource->weather),
        ];
    }
}
