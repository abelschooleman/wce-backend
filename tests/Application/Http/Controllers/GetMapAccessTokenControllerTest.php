<?php

namespace Tests\Application\Http\Controllers;

use Tests\TestCase;

class GetMapAccessTokenControllerTest extends TestCase
{
    public function testGetMapAccessTokenReturnsToken(): void
    {
        $this->getJson('api/access-token')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'token' => 'gmaps-token',
                ]
            ]);
    }
}
