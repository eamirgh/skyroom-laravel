<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class FacadeAndServiceProviderTest extends TestCase
{
    public function test_skyroom_client_is_bound_as_singleton_in_container(): void
    {
        $instance1 = $this->app->make(SkyroomClient::class);
        $instance2 = $this->app->make('skyroom');

        $this->assertInstanceOf(SkyroomClient::class, $instance1);
        $this->assertSame($instance1, $instance2);
    }

    public function test_facade_resolves_and_forwards_calls(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/*' => Http::response([
                'ok' => true,
                'result' => [
                    ['id' => 1, 'name' => 'meeting'],
                ],
            ]),
        ]);

        $rooms = Skyroom::getRooms();
        $this->assertSame([['id' => 1, 'name' => 'meeting']], $rooms);

        $resourceRooms = Skyroom::rooms()->all();
        $this->assertSame([['id' => 1, 'name' => 'meeting']], $resourceRooms);
    }

    public function test_config_publishing(): void
    {
        $targetConfig = config_path('skyroom.php');
        if (File::exists($targetConfig)) {
            File::delete($targetConfig);
        }

        Artisan::call('vendor:publish', ['--tag' => 'skyroom-config']);

        $this->assertFileExists($targetConfig);

        if (File::exists($targetConfig)) {
            File::delete($targetConfig);
        }
    }
}
