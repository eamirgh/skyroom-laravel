<?php

namespace Skyroom\Laravel\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Enums\ErrorCode;
use Skyroom\Laravel\Enums\Gender;
use Skyroom\Laravel\Enums\Status;

class EnumsTest extends TestCase
{
    public function test_access_level_enum_values(): void
    {
        $this->assertSame(1, AccessLevel::NORMAL->value);
        $this->assertSame(2, AccessLevel::PRESENTER->value);
        $this->assertSame(3, AccessLevel::OPERATOR->value);
    }

    public function test_status_enum_values(): void
    {
        $this->assertSame(0, Status::INACTIVE->value);
        $this->assertSame(1, Status::ACTIVE->value);
    }

    public function test_gender_enum_values(): void
    {
        $this->assertSame(0, Gender::UNKNOWN->value);
        $this->assertSame(1, Gender::MALE->value);
        $this->assertSame(2, Gender::FEMALE->value);
    }

    public function test_error_code_enum_values(): void
    {
        $this->assertSame(10, ErrorCode::SERVICE_DISABLED->value);
        $this->assertSame(11, ErrorCode::INVALID_API_KEY->value);
        $this->assertSame(12, ErrorCode::UNAUTHORIZED->value);
        $this->assertSame(13, ErrorCode::INVALID_REQUEST->value);
        $this->assertSame(14, ErrorCode::INTERNAL_ERROR->value);
        $this->assertSame(15, ErrorCode::NOT_FOUND->value);
    }
}
