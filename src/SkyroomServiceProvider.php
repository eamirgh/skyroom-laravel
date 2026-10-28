<?php

namespace Skyroom\Laravel;

use Illuminate\Support\ServiceProvider;

class SkyroomServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/skyroom.php',
            'skyroom'
        );

        $this->app->singleton(SkyroomClient::class, function ($app) {
            $config = $app['config']['skyroom'] ?? [];

            return new SkyroomClient(
                apiKey: (string) ($config['api_key'] ?? ''),
                baseUrl: (string) ($config['base_url'] ?? 'https://www.skyroom.online/skyroom/api/'),
                timeout: (int) ($config['timeout'] ?? 30),
                retry: (array) ($config['retry'] ?? ['times' => 3, 'sleep_milliseconds' => 100]),
            );
        });

        $this->app->alias(SkyroomClient::class, 'skyroom');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/skyroom.php' => $this->app->configPath('skyroom.php'),
            ], 'skyroom-config');
        }
    }
}
