<?php

namespace App\Domain\Objects;

readonly class CurrentCityWeather
{
    /**
     * @param array<Weather> $weather
     */
    public function __construct(
        public Humidity $humidity,
        public Temperature $temperature,
        public array $weather,
    ) {}
}
