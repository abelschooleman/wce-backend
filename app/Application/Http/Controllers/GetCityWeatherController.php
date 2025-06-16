<?php

namespace App\Application\Http\Controllers;

use App\Application\Http\Resources\WeatherResource;
use App\Application\Weather\FetchCurrentWeatherInCity;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\City;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Coordinates;
use App\Domain\Objects\Country;
use App\Domain\Objects\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GetCityWeatherController
{
    public function __invoke(Request $request, WeatherApiInterface $api, FetchCurrentWeatherInCity $service): WeatherResource
    {
        $validator = Validator::make($request->query(), [
            'name' => 'required',
            'country' => 'required',
            'state' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            abort(400, 'No city name provided');
        }

        if (!$api->isAvailable()) {
            abort(503, 'Weather API is not available');
        }

        try {
            $city = new City(
                new Coordinates($request->get('latitude'), $request->get('longitude')),
                new CityName($request->get('name')),
                new Country($request->get('country')),
                new State($request->get('state')),
            );

            return new WeatherResource($service($api, $city));
        } catch (Throwable $exception) {
            abort($exception->getPrevious()->getCode());
        }
    }
}
