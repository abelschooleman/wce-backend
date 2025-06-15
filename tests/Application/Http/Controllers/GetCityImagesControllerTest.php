<?php

namespace Tests\Application\Http\Controllers;

use App\Application\Images\FetchImageCollectionOfCity;
use App\Domain\Objects\Image;
use App\Domain\Objects\ImageCollection;
use App\Domain\Objects\ImageDescription;
use App\Domain\Objects\ImageUrl;
use Exception;
use Mockery;
use Tests\TestCase;

class GetCityImagesControllerTest extends TestCase
{
    public function testControllerReturnsCollectionOfImages(): void
    {
        $this->instance(FetchImageCollectionOfCity::class, Mockery::mock(FetchImageCollectionOfCity::class, function ($mock) {
            $mock->expects('__invoke')
                ->once()
                ->andReturn(new ImageCollection([
                    new Image(new ImageUrl('raw1'), new ImageUrl('full1'), new ImageUrl('regular1'), new ImageUrl('small1'), new ImageUrl('thumbnail1'), new ImageDescription('description1')),
                    new Image(new ImageUrl('raw2'), new ImageUrl('full2'), new ImageUrl('regular2'), new ImageUrl('small2'), new ImageUrl('thumbnail2'), new ImageDescription('description2')),
                ]));
        }));

        $this->get('api/images?city=TestCity')
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    [
                        'description' => 'description1',
                        'full'  => 'full1',
                        'raw'  => 'raw1',
                        'regular' => 'regular1',
                        'small' => 'small1',
                        'thumbnail' => 'thumbnail1',
                    ],
                    [
                        'description' => 'description2',
                        'full'  => 'full2',
                        'raw'  => 'raw2',
                        'regular' => 'regular2',
                        'small' => 'small2',
                        'thumbnail' => 'thumbnail2',
                    ],
                ]
            ]);
    }

    public function testBadRequestIsReturnedWhenCityIsNotProvided(): void
    {
        $this->get('api/images')
            ->assertBadRequest();
    }

    public function testPreviousErrorCodeIsReturnedWhenUnexpectedExceptionOccurrs(): void
    {
        $this->instance(FetchImageCollectionOfCity::class, Mockery::mock(FetchImageCollectionOfCity::class, function ($mock) {
            $mock->expects('__invoke')
                ->once()
                ->andThrow(Exception::class);
        }));

        $this->get('api/images?city=TestCity')
            ->assertStatus(500);
    }
}
