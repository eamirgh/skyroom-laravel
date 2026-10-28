<?php

namespace Skyroom\Laravel\Enums;

enum Gender: int
{
    case UNKNOWN = 0;
    case MALE = 1;
    case FEMALE = 2;

    public function description(): string
    {
        return match ($this) {
            self::UNKNOWN => 'نامعلوم',
            self::MALE => 'مرد',
            self::FEMALE => 'زن',
        };
    }
}
