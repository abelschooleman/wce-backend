<?php

namespace App\Application\Http\Controllers;

use App\Application\GetMapAccessToken;
use App\Application\Http\Resources\MapAccessTokenResource;

class GetMapAccessTokenController
{
    public function __invoke(GetMapAccessToken $service)
    {
        return new MapAccessTokenResource($service());
    }
}
