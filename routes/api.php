<?php

use App\Application\Http\Controllers\FetchCityWeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/weather', FetchCityWeatherController::class);
