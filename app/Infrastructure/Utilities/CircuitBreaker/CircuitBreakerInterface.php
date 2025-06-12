<?php

namespace App\Infrastructure\Utilities\CircuitBreaker;

interface CircuitBreakerInterface
{
    public function getId(): string;

    public function isAvailable(): bool;

    public function setClosed(): void;

    public function setOpened(): void;
}
