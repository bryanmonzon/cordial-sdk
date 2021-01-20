<?php

namespace Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Cordial\Exceptions\FailedActionException;
use Cordial\Exceptions\NotFoundException;
use Cordial\Exceptions\ValidationException;
use Cordial\Cordial;
use Mockery;
use PHPUnit\Framework\TestCase;

class CordialSDKTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_making_basic_requests()
    {
        $cordial = new Cordial('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'contacts', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->once()->andReturn(200);
        $response->shouldReceive('getBody')->once()->andReturn('[{"key": "value"}]');

        $this->assertCount(1, $cordial->contacts());
    }

    public function test_handling_validation_errors()
    {
        $cordial = new Cordial('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'contacts', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(422);
        $response->shouldReceive('getBody')->once()->andReturn('{"name": ["The name is required."]}');

        try {
            $cordial->contacts();
        } catch (ValidationException $e) {
        }

        $this->assertEquals(['name' => ['The name is required.']], $e->errors());
    }

    public function test_handling_404_errors()
    {
        $this->expectException(NotFoundException::class);

        $cordial = new Cordial('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'contacts', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(404);

        $cordial->contacts();
    }

    public function test_handling_failed_action_errors()
    {
        $cordial = new Cordial('123', $http = Mockery::mock(Client::class));

        $http->shouldReceive('request')->once()->with('GET', 'contacts', [])->andReturn(
            $response = Mockery::mock(Response::class)
        );

        $response->shouldReceive('getStatusCode')->andReturn(400);
        $response->shouldReceive('getBody')->once()->andReturn('Error!');

        try {
            $cordial->contacts();
        } catch (FailedActionException $e) {
            $this->assertSame('Error!', $e->getMessage());
        }
    }
}
