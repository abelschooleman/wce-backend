<?php

use App\Application\Http\Controllers\GetCityImagesController;
use App\Application\Http\Controllers\GetCityWeatherController;
use App\Application\Http\Controllers\FindCityByNameController;
use App\Application\Http\Controllers\GetMapAccessTokenController;
use Illuminate\Support\Facades\Route;

Route::get('/access-token', GetMapAccessTokenController::class);
Route::get('/find-city', FindCityByNameController::class);
Route::get('/images', GetCityImagesController::class);
Route::get('/weather', GetCityWeatherController::class);
