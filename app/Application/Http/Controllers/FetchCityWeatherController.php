<?php

namespace App\Application\Http\Controllers;

use App\Application\Http\Resources\WeatherResource;
use App\Application\Weather\FetchCurrentWeatherInCity;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FetchCityWeatherController
{
    public function __invoke(Request $request, WeatherApiInterface $api, FetchCurrentWeatherInCity $service): WeatherResource
    {
        if (!$this->validate($request)) {
            abort(400, 'No city name provided');
        }

        if (!$api->isAvailable()) {
            abort(503, 'Weather API is not available');
        }

        $city = new City($request->get('city'));

        return new WeatherResource($service($api, $city));
    }

    private function validate(Request $request): bool
    {
        return Validator::make($request->query(), [
            'city' => 'required',
        ])
            ->passes();
    }
}
