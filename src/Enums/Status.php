<?php

namespace Skyroom\Laravel\Enums;

enum Status: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;

    public function description(): string
    {
        return match ($this) {
            self::INACTIVE => 'غیرفعال',
            self::ACTIVE => 'فعال',
        };
    }
}
