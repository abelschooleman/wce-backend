<?php

namespace App\Domain\Objects;

readonly class City
{
    public function __construct(
        public CityName $name,
        public Country $country,
        public State $state,
        public Coordinates $coordinates,
    ) {}
}
