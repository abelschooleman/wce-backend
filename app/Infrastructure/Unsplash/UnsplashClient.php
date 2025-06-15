<?php

namespace App\Infrastructure\Unsplash;

use App\Domain\Interfaces\ImageApiInterface;
use App\Domain\Objects\CityName;
use App\Domain\Objects\Image;
use App\Domain\Objects\ImageCollection;
use App\Domain\Objects\ImageDescription;
use App\Domain\Objects\ImageUrl;
use Throwable;
use Unsplash\HttpClient;
use Unsplash\Search;

class UnsplashClient implements ImageApiInterface
{
    public function __construct()
    {
        HttpClient::init([
            'applicationId'	=> config('images.unsplash.access_key'),
            'secret'	=> config('images.unsplash.secret_key'),
            'utmSource' => config('images.unsplash.utm_source'),
        ]);
    }

    /**
     * @throws UnsplashClientException
     */
    public function fetchImageCollection(CityName $city): ImageCollection
    {
        $searchTerm = $city->value;
        $page = 1;
        $perPage = 20;
        $orientation = 'squarish';

        try {
            $response = Search::photos($searchTerm, $page, $perPage, $orientation);

            $images = array_map(function ($image) {
                return new Image(
                    new ImageUrl($image['urls']['raw']),
                    new ImageUrl($image['urls']['full']),
                    new ImageUrl($image['urls']['regular']),
                    new ImageUrl($image['urls']['small']),
                    new ImageUrl($image['urls']['thumb']),
                    new ImageDescription($image['description'] ?? $image['alt_description'] ?? null),
                );
            }, $response->getResults());

            return new ImageCollection($images);
        } catch (Throwable $exception) {
            throw new UnsplashClientException('An error occurred when fetching images from Unsplash API: ' . $exception->getMessage(), $exception->getCode(), previous: $exception);
        }
    }
}
