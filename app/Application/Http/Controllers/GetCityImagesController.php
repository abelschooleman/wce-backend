<?php

namespace App\Application\Http\Controllers;

use App\Application\Http\Resources\ImageCollection;
use App\Application\Images\FetchImageCollectionOfCity;
use App\Domain\Interfaces\ImageApiInterface;
use App\Domain\Objects\CityName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;

class GetCityImagesController
{
    public function __invoke(Request $request, ImageApiInterface $api, FetchImageCollectionOfCity $service): ImageCollection
    {
        $validator = Validator::make($request->query(), [
            'city' => 'required',
        ]);

        if ($validator->fails()) {
            abort(400, 'No city name provided');
        }

        try {
            $city = new CityName($request->get('city'));

            return new ImageCollection($service($api, $city)->images);
        } catch (Throwable $exception) {
            abort($exception->getPrevious()->getCode());
        }
    }
}
