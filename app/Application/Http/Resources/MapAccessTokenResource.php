<?php

namespace App\Application\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapAccessTokenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'token' => $this->resource,
        ];
    }
}
