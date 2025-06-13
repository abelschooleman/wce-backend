<?php

namespace Tests\Application\Images;

use App\Application\Images\FetchImageCollectionOfCity;
use App\Domain\Interfaces\ImageApiInterface;
use App\Domain\Objects\City;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FetchImageCollectionOfCityTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCollectionIsFetched(): void
    {
        $city = new City('TestCity');

        /* @var ImageApiInterface&MockObject $api */
        $api = $this->createMock(ImageApiInterface::class);

        $api->expects(self::once())
            ->method('fetchImageCollection')
            ->with($city);

        new FetchImageCollectionOfCity()($api, $city);
    }
}
