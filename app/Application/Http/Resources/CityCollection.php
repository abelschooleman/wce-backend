<?php

namespace App\Application\Http\Resources;

use App\Domain\Objects\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CityCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection
                ->map(fn (City $city) => [
                    'name' => $city->name->value,
                    'country' => $city->country->value,
                    'state' => $city->state->value,
                    'latitude' => $city->coordinates->latitude,
                    'longitude' => $city->coordinates->longitude,
                ]),
        ];
    }
}
