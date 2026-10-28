<?php

namespace Skyroom\Laravel\Enums;

enum AccessLevel: int
{
    case NORMAL = 1;
    case PRESENTER = 2;
    case OPERATOR = 3;

    public function description(): string
    {
        return match ($this) {
            self::NORMAL => 'کاربر عادی',
            self::PRESENTER => 'ارایه دهنده',
            self::OPERATOR => 'اپراتور',
        };
    }
}
