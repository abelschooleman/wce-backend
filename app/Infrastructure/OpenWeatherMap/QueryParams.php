<?php

namespace App\Infrastructure\OpenWeatherMap;

readonly class QueryParams
{
    public function __construct(
        private string $query,
    ) {}

    public function toQueryString(): string
    {
        return http_build_query(['q' => $this->query]);
    }
}
