<?php

namespace Skyroom\Laravel\Exceptions;

use Throwable;

class SkyroomApiException extends SkyroomException
{
    public function __construct(
        string $message,
        protected int $errorCode = 0,
        protected string $action = '',
        protected array $params = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $errorCode, $previous);
    }

    public static function fromResponse(
        int $code,
        string $message,
        string $action = '',
        array $params = [],
    ): self {
        return match ($code) {
            10 => new ServiceDisabledException($message, $code, $action, $params),
            11 => new AuthenticationException($message, $code, $action, $params),
            12 => new UnauthorizedException($message, $code, $action, $params),
            13 => new ValidationException($message, $code, $action, $params),
            14 => new ServerException($message, $code, $action, $params),
            15 => new NotFoundException($message, $code, $action, $params),
            default => new self($message, $code, $action, $params),
        };
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
