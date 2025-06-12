<?php

namespace Tests\Domain\Objects;

use App\Domain\Objects\Temperature;
use PHPUnit\Framework\TestCase;

class TemperatureTest extends TestCase
{
    public function testToCelciusReturnsTemperatureInCelciusWithTwoDecimals(): void
    {
        $temperature = new Temperature(400);

        self::assertSame(126.85, $temperature->toCelcius());
    }
}
