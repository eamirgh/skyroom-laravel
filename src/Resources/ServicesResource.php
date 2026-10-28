<?php

namespace Skyroom\Laravel\Resources;

use Skyroom\Laravel\Contracts\SkyroomClientContract;

class ServicesResource
{
    public function __construct(
        protected SkyroomClientContract $client,
    ) {
    }

    /**
     * Get list of available services.
     */
    public function all(): array
    {
        return (array) $this->client->call('getServices');
    }
}
