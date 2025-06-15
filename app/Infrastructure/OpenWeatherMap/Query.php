<?php

namespace App\Infrastructure\OpenWeatherMap;

readonly class Query
{
    public function __construct(
        private array $params = [],
    ) {}

    public function toQueryString(): string
    {
        return http_build_query($this->params);
    }
}
