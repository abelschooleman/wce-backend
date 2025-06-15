<?php

namespace App\Application\Http\Controllers;

use App\Application\Geocode\FindCityByName;
use App\Application\Http\Resources\CityCollection;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Domain\Objects\CityName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class FindCityByNameController
{
    public function __invoke(Request $request, WeatherApiInterface $api, FindCityByName $service): CityCollection
    {
        $validator = Validator::make($request->query(), [
            'query' => 'required',
        ]);

        if ($validator->fails()) {
            abort(400, 'No city name provided');
        }

        if (!$api->isAvailable()) {
            abort(503, 'Weather API is not available');
        }

        try {
            $city = new CityName($request->get('query'));

            return new CityCollection($service($api, $city));
        } catch (Throwable $exception) {
            abort($exception->getPrevious()->getCode());
        }
    }
}
