<?php

namespace App\Application\Http\Resources;

use App\Domain\Objects\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ImageCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function (Image $image) {
                return [
                    'raw'  => $image->raw->value,
                    'full' => $image->full->value,
                    'regular' => $image->regular->value,
                    'small' => $image->small->value,
                    'thumbnail' => $image->thumbnail->value,
                    'description' => $image->description->value,
                ];
            }),
        ];
    }
}
