<?php

namespace Tests\Infrastructure\Utilities\CircuitBreaker;

use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreaker;
use App\Infrastructure\Utilities\CircuitBreaker\CircuitBreakerInterface;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class CircuitBreakerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new class implements CircuitBreakerInterface
        {
            use CircuitBreaker;

            function getId(): string
            {
                return 'test_circuit_breaker';
            }
        };
    }

    public function testSetOpenedSetsServiceToCurrentTimeInRedis(): void
    {
        Redis::expects('set')
            ->once()
            ->withSomeOfArgs('cb:'.$this->service->getId());

        $this->service->setOpened();
    }

    public function testSetClosedRemovesServiceFromRedis(): void
    {
        Redis::expects('del')
            ->once()
            ->with('cb:'.$this->service->getId());

        $this->service->setClosed();
    }

    public function testIsAvailableReturnsTrueWhenServiceIsNotSetInRedis(): void
    {
        Redis::expects('get')
            ->once()
            ->with('cb:'.$this->service->getId())
            ->andReturn(null);

        self::assertTrue($this->service->isAvailable());
    }

    public function testIsAvailableReturnsTrueWhenServiceIsSetInRedisAndTimeoutHasExpired(): void
    {
        Redis::expects('get')
            ->once()
            ->with('cb:'.$this->service->getId())
            ->andReturn(time() - 60000);

        self::assertTrue($this->service->isAvailable());
    }

    public function testIsAvailableReturnsFalseWhenServiceIsSetInRedisAndTimeoutHasNotYetExpired(): void
    {
        Redis::expects('get')
            ->once()
            ->with('cb:'.$this->service->getId())
            ->andReturn(time() - 20000);

        self::assertFalse($this->service->isAvailable());
    }
}
