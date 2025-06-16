<?php

namespace App\Domain\Objects;

readonly class City
{
    public function __construct(
        public Coordinates $coordinates,
        public CityName $name,
        public Country $country,
        public ?State $state,
    ) {}
}
