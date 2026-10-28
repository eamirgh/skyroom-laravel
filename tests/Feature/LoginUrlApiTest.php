<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class LoginUrlApiTest extends TestCase
{
    protected SkyroomClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new SkyroomClient(
            apiKey: 'test-api-key',
            baseUrl: 'https://www.skyroom.online/skyroom/api/'
        );
    }

    public function test_create_login_url(): void
    {
        $expectedUrl = 'https://www.skyroom.online/ch/paramooz/meeting/t/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9';

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $expectedUrl,
            ]),
        ]);

        $url = $this->client->createLoginUrl([
            'room_id' => 1,
            'user_id' => 'sina',
            'nickname' => 'Sina',
            'access' => AccessLevel::OPERATOR->value,
            'concurrent' => 1,
            'language' => 'en',
            'ttl' => 3600,
        ]);

        $this->assertSame($expectedUrl, $url);

        Http::assertSent(function ($req) {
            return $req['action'] === 'createLoginUrl'
                && $req['params']['room_id'] === 1
                && $req['params']['user_id'] === 'sina'
                && $req['params']['access'] === 3
                && $req['params']['ttl'] === 3600;
        });
    }
}
