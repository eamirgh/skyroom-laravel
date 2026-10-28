<?php

namespace Skyroom\Laravel\Contracts;

use Skyroom\Laravel\Resources\RoomsResource;
use Skyroom\Laravel\Resources\ServicesResource;
use Skyroom\Laravel\Resources\UsersResource;

interface SkyroomClientContract
{
    public function call(string $action, array $params = []): mixed;

    public function services(): ServicesResource;

    public function rooms(): RoomsResource;

    public function users(): UsersResource;

    public function createLoginUrl(array $params): string;
}
