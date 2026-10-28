# Agent Instructions for `skyroom-laravel`

This document outlines architectural guidelines, project conventions, and operational workflows for AI agents working in this repository.

---

## 1. Project Overview

`eamirgh/skyroom-laravel` is a test-driven, production-ready Laravel wrapper package for the **Skyroom Web Service API** (virtual classroom and webinar platform).

### Tech Stack
- **Language**: PHP `>= 8.2`
- **Framework compatibility**: Laravel `^10.0`, `^11.0`, `^12.0`, and `^13.0`
- **Testing**: PHPUnit `^10.5|^11.0`, Orchestra Testbench `^8.0|^9.0|^10.0|^11.0`
- **Static Analysis**: PHPStan `^2.3` (Level 6+)

---

## 2. Architecture & File Layout

```
.
├── config/
│   └── skyroom.php                  # Default configuration file published to Laravel apps
├── src/
│   ├── Contracts/
│   │   └── SkyroomClientContract.php # Contract implemented by SkyroomClient and SkyroomFake
│   ├── Enums/
│   │   ├── AccessLevel.php          # 1: Normal, 2: Presenter, 3: Operator
│   │   ├── ErrorCode.php            # 10..15 Skyroom error codes
│   │   ├── Gender.php               # 0: Unknown, 1: Male, 2: Female
│   │   └── Status.php               # 0: Inactive, 1: Active
│   ├── Exceptions/
│   │   ├── SkyroomException.php     # Base package exception
│   │   ├── SkyroomApiException.php  # API error factory and base class
│   │   ├── AuthenticationException.php # Code 11 or missing API key
│   │   ├── UnauthorizedException.php   # Code 12
│   │   ├── ValidationException.php     # Code 13
│   │   ├── ServerException.php         # Code 14
│   │   ├── NotFoundException.php       # Code 15
│   │   ├── ServiceDisabledException.php# Code 10
│   │   ├── RateLimitException.php      # HTTP 503 rate limit
│   │   └── NetworkException.php        # Connection or HTTP failure
│   ├── Facades/
│   │   └── Skyroom.php              # Public facade with fake() support
│   ├── Resources/
│   │   ├── RoomsResource.php        # Room CRUD, URLs, access management
│   │   ├── ServicesResource.php     # Service list retrieval
│   │   └── UsersResource.php        # User CRUD, room assignments
│   ├── Testing/
│   │   └── SkyroomFake.php          # In-memory test double with assertion helpers
│   ├── SkyroomClient.php            # Primary client implementation
│   └── SkyroomServiceProvider.php   # Laravel package service provider
└── tests/
    ├── Feature/                     # HTTP and resource integration tests
    ├── Unit/                        # Enums and exceptions unit tests
    └── TestCase.php                 # Base testbench case
```

---

## 3. Skyroom API Protocol Rules

1. **Endpoint**: `POST https://www.skyroom.online/skyroom/api/{api_key}`
2. **Payload format**:
   ```json
   {
     "action": "actionName",
     "params": { ... }
   }
   ```
   *Note: Omit `params` if empty.*
3. **Responses**:
   - Status 200 with `{"ok": true, "result": ...}` on success.
   - Status 200 with `{"ok": false, "error_code": int, "error_message": string}` on API-level errors.
   - Status 503 when rate limited (> 500 requests/minute) or during service outages.
4. **Error Code Mapping**:
   - `10` -> `ServiceDisabledException`
   - `11` -> `AuthenticationException`
   - `12` -> `UnauthorizedException`
   - `13` -> `ValidationException`
   - `14` -> `ServerException`
   - `15` -> `NotFoundException`

---

## 4. Development & Testing Rules

- **Test-Driven Development (TDD)**: Always write a failing test before introducing new features or bug fixes.
- **Zero Regression**: Every test in `tests/` must pass.
- **Static Analysis**: All code in `src/` must pass `phpstan` analysis with zero errors.
- **Minimal Code / YAGNI**: Avoid unnecessary layers, abstractions, or dependencies.

### Common Commands

```bash
# Run test suite
composer test

# Run static analysis
composer analyse

# Run code style checks & fixes
composer format-test
composer format

# Validate composer
composer validate --strict

# Run specific test file
./vendor/bin/phpunit tests/Feature/RoomsApiTest.php
```
