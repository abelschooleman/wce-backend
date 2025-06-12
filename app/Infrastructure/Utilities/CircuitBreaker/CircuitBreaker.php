<?php

namespace App\Infrastructure\Utilities\CircuitBreaker;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

trait CircuitBreaker
{
    /**
     * Timeout in milliseconds
     */
    private const int TIMEOUT = 30000;

    public function isAvailable(): bool
    {
        $state = Redis::get($this->key());

        return is_null($state) || time() - (int) $state > self::TIMEOUT;
    }

    public function setClosed(): void
    {
        Redis::del($this->key());
    }

    public function setOpened(): void
    {
        Redis::set($this->key(), Carbon::now()->unix());
    }

    private function key(): string
    {
        return sprintf('cb:%s', str_replace('\\', '_', strtolower(static::getId())));
    }
}
