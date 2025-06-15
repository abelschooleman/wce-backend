<?php

namespace App\Application;

class GetMapAccessToken
{
    public function __invoke(): string
    {
        return config('maps.google_maps.access_token');
    }
}
