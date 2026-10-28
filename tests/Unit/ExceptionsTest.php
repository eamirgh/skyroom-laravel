<?php

namespace Skyroom\Laravel\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Skyroom\Laravel\Exceptions\AuthenticationException;
use Skyroom\Laravel\Exceptions\NotFoundException;
use Skyroom\Laravel\Exceptions\RateLimitException;
use Skyroom\Laravel\Exceptions\ServerException;
use Skyroom\Laravel\Exceptions\ServiceDisabledException;
use Skyroom\Laravel\Exceptions\SkyroomApiException;
use Skyroom\Laravel\Exceptions\SkyroomException;
use Skyroom\Laravel\Exceptions\UnauthorizedException;
use Skyroom\Laravel\Exceptions\ValidationException;

class ExceptionsTest extends TestCase
{
    public function test_factory_creates_service_disabled_exception(): void
    {
        $e = SkyroomApiException::fromResponse(10, 'وب‌سرویس غیرفعال است.', 'getRooms');
        $this->assertInstanceOf(ServiceDisabledException::class, $e);
        $this->assertSame(10, $e->getErrorCode());
        $this->assertSame('وب‌سرویس غیرفعال است.', $e->getMessage());
        $this->assertSame('getRooms', $e->getAction());
    }

    public function test_factory_creates_authentication_exception(): void
    {
        $e = SkyroomApiException::fromResponse(11, 'کلید وب‌سرویس نامعتبر است.', 'getRooms');
        $this->assertInstanceOf(AuthenticationException::class, $e);
        $this->assertSame(11, $e->getErrorCode());
    }

    public function test_factory_creates_unauthorized_exception(): void
    {
        $e = SkyroomApiException::fromResponse(12, 'درخواست شما غیرمجاز است.', 'createRoom');
        $this->assertInstanceOf(UnauthorizedException::class, $e);
        $this->assertSame(12, $e->getErrorCode());
    }

    public function test_factory_creates_validation_exception(): void
    {
        $e = SkyroomApiException::fromResponse(13, 'درخواست شما معتبر نیست.', 'createRoom', ['name' => 'bad']);
        $this->assertInstanceOf(ValidationException::class, $e);
        $this->assertSame(13, $e->getErrorCode());
        $this->assertSame(['name' => 'bad'], $e->getParams());
    }

    public function test_factory_creates_server_exception(): void
    {
        $e = SkyroomApiException::fromResponse(14, 'درخواست شما با خطا روبرو شد.', 'deleteRoom');
        $this->assertInstanceOf(ServerException::class, $e);
        $this->assertSame(14, $e->getErrorCode());
    }

    public function test_factory_creates_not_found_exception(): void
    {
        $e = SkyroomApiException::fromResponse(15, 'داده مورد نظر پیدا نشد.', 'getRoom', ['room_id' => 999]);
        $this->assertInstanceOf(NotFoundException::class, $e);
        $this->assertSame(15, $e->getErrorCode());
        $this->assertSame(['room_id' => 999], $e->getParams());
    }

    public function test_factory_creates_generic_api_exception_for_unknown_code(): void
    {
        $e = SkyroomApiException::fromResponse(99, 'خطای ناشناخته', 'customAction');
        $this->assertInstanceOf(SkyroomApiException::class, $e);
        $this->assertSame(99, $e->getErrorCode());
    }

    public function test_rate_limit_exception_inherits_skyroom_exception(): void
    {
        $e = new RateLimitException('Rate limit exceeded (503)', 503);
        $this->assertInstanceOf(SkyroomException::class, $e);
        $this->assertSame(503, $e->getCode());
    }
}
