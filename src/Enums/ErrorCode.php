<?php

namespace Skyroom\Laravel\Enums;

enum ErrorCode: int
{
    case SERVICE_DISABLED = 10;
    case INVALID_API_KEY = 11;
    case UNAUTHORIZED = 12;
    case INVALID_REQUEST = 13;
    case INTERNAL_ERROR = 14;
    case NOT_FOUND = 15;

    public function description(): string
    {
        return match ($this) {
            self::SERVICE_DISABLED => 'وب‌سرویس غیرفعال است.',
            self::INVALID_API_KEY => 'کلید وب‌سرویس نامعتبر است.',
            self::UNAUTHORIZED => 'درخواست شما غیرمجاز است.',
            self::INVALID_REQUEST => 'درخواست شما معتبر نیست.',
            self::INTERNAL_ERROR => 'درخواست شما با خطا روبرو شد.',
            self::NOT_FOUND => 'داده مورد نظر پیدا نشد.',
        };
    }
}
