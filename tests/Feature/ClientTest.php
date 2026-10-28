<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\Exceptions\AuthenticationException;
use Skyroom\Laravel\Exceptions\NotFoundException;
use Skyroom\Laravel\Exceptions\RateLimitException;
use Skyroom\Laravel\Exceptions\ValidationException;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_throws_exception_if_api_key_is_missing(): void
    {
        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Skyroom API key is not configured.');

        $client = new SkyroomClient(apiKey: '', baseUrl: 'https://www.skyroom.online/skyroom/api/');
        $client->call('getServices');
    }

    public function test_successful_call_returns_result(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-key' => Http::response([
                'ok' => true,
                'result' => [
                    ['id' => 1, 'title' => 'Service 1'],
                ],
            ], 200),
        ]);

        $client = new SkyroomClient(apiKey: 'test-key', baseUrl: 'https://www.skyroom.online/skyroom/api/');
        $result = $client->call('getServices');

        $this->assertSame([['id' => 1, 'title' => 'Service 1']], $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.skyroom.online/skyroom/api/test-key'
                && $request->method() === 'POST'
                && $request['action'] === 'getServices'
                && !isset($request['params']);
        });
    }

    public function test_call_with_params_sends_params_in_payload(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-key' => Http::response([
                'ok' => true,
                'result' => 42,
            ], 200),
        ]);

        $client = new SkyroomClient(apiKey: 'test-key', baseUrl: 'https://www.skyroom.online/skyroom/api/');
        $result = $client->call('createRoom', ['name' => 'room-1', 'title' => 'Room 1']);

        $this->assertSame(42, $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://www.skyroom.online/skyroom/api/test-key'
                && $request['action'] === 'createRoom'
                && $request['params'] === ['name' => 'room-1', 'title' => 'Room 1'];
        });
    }

    public function test_api_error_response_throws_typed_exception(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-key' => Http::response([
                'ok' => false,
                'error_code' => 15,
                'error_message' => 'داده مورد نظر پیدا نشد.',
            ], 200),
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionCode(15);
        $this->expectExceptionMessage('داده مورد نظر پیدا نشد.');

        $client = new SkyroomClient(apiKey: 'test-key', baseUrl: 'https://www.skyroom.online/skyroom/api/');
        $client->call('getRoom', ['room_id' => 999]);
    }

    public function test_api_validation_error_throws_validation_exception(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-key' => Http::response([
                'ok' => false,
                'error_code' => 13,
                'error_message' => 'درخواست شما معتبر نیست.',
            ], 200),
        ]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(13);

        $client = new SkyroomClient(apiKey: 'test-key', baseUrl: 'https://www.skyroom.online/skyroom/api/');
        $client->call('createRoom', ['name' => '']);
    }

    public function test_rate_limit_503_throws_rate_limit_exception(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-key' => Http::response('Service Unavailable', 503),
        ]);

        $this->expectException(RateLimitException::class);

        $client = new SkyroomClient(apiKey: 'test-key', baseUrl: 'https://www.skyroom.online/skyroom/api/', retry: ['times' => 1, 'sleep_milliseconds' => 0]);
        $client->call('getRooms');
    }
}
