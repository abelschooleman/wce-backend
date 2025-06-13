<?php

namespace App\Providers;

use App\Domain\Interfaces\ImageApiInterface;
use App\Domain\Interfaces\WeatherApiInterface;
use App\Infrastructure\OpenWeatherMap\OpenWeatherMapClient;
use App\Infrastructure\Unsplash\UnsplashClient;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImageApiInterface::class, UnsplashClient::class);
        $this->app->bind(WeatherApiInterface::class, OpenWeatherMapClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
