<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class ServicesApiTest extends TestCase
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

    public function test_get_services_via_resource(): void
    {
        $servicesResponse = [
            [
                'id' => 1,
                'title' => 'سرویس وب کنفرانس',
                'status' => 1,
                'user_limit' => 10,
                'video_limit' => 8,
                'time_limit' => null,
                'time_usage' => 2213762,
                'start_time' => 1582851460,
                'stop_time' => 1580259460,
                'create_time' => 1582851460,
                'update_time' => 1580259460,
            ],
            [
                'id' => 2,
                'title' => 'سرویس آموزش آنلاین',
                'status' => 0,
                'user_limit' => 100,
                'video_limit' => 3,
                'time_limit' => null,
                'time_usage' => 21124,
                'start_time' => 1580259460,
                'stop_time' => 1582851460,
                'create_time' => 1479021195,
                'update_time' => 1582851460,
            ],
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $servicesResponse,
            ]),
        ]);

        $result = $this->client->services()->all();

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]['id']);
        $this->assertSame('سرویس آموزش آنلاین', $result[1]['title']);

        // Check convenience method
        $this->assertSame($servicesResponse, $this->client->getServices());
    }
}
