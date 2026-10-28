<?php

namespace Skyroom\Laravel\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Skyroom\Laravel\SkyroomServiceProvider;
use Skyroom\Laravel\Facades\Skyroom;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            SkyroomServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Skyroom' => Skyroom::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('skyroom.api_key', 'test-api-key-1234567890123456789012345678901234567890');
        $app['config']->set('skyroom.base_url', 'https://www.skyroom.online/skyroom/api/');
        $app['config']->set('skyroom.timeout', 15);
        $app['config']->set('skyroom.retry', [
            'times' => 1,
            'sleep_milliseconds' => 0,
        ]);
    }
}
