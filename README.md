# Skyroom Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/eamirgh/skyroom-laravel.svg?style=flat-square)](https://packagist.org/packages/eamirgh/skyroom-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/eamirgh/skyroom-laravel/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/eamirgh/skyroom-laravel/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/eamirgh/skyroom-laravel.svg?style=flat-square)](https://packagist.org/packages/eamirgh/skyroom-laravel)
[![License](https://img.shields.io/packagist/l/eamirgh/skyroom-laravel.svg?style=flat-square)](https://packagist.org/packages/eamirgh/skyroom-laravel)

Production-ready, test-driven Laravel package wrapper for the [Skyroom Web Service API](https://www.skyroom.online).

---

## Features

- **Full API Coverage**: 100% implementation of Skyroom Web Service specifications (Services, Rooms, Users, Direct Login URLs).
- **Expressive Fluent Interface**: Clean resource managers (`rooms()`, `users()`, `services()`) plus direct forwarding helpers.
- **Typed Enums & Exceptions**: Strongly typed PHP 8.2+ enums (`AccessLevel`, `Status`, `Gender`, `ErrorCode`) and domain exceptions (`NotFoundException`, `ValidationException`, `RateLimitException`, `AuthenticationException`, `ServerException`, etc.).
- **Built-in Mocking & Testing**: First-class `Skyroom::fake()` with recorded call inspection and assertions (`assertSent`, `assertNotSent`, `assertSentCount`), along with full compatibility with Laravel's `Http::fake()`.
- **Automatic Resilience**: Configurable timeout, automatic retries with backoff on connection drops and 503 rate limits.
- **Strictly Typed & Tested**: 100% test coverage with PHPUnit and level 6 static analysis with PHPStan.

---

## Requirements

- PHP `^8.2`
- Laravel `^10.0`, `^11.0`, or `^12.0`

---

## Installation

Install package via Composer:

```bash
composer require eamirgh/skyroom-laravel
```

Publish configuration file:

```bash
php artisan vendor:publish --tag="skyroom-config"
```

Add your Skyroom API key to your `.env` file:

```env
SKYROOM_API_KEY=your-50-character-skyroom-api-key
SKYROOM_BASE_URL=https://www.skyroom.online/skyroom/api/
SKYROOM_TIMEOUT=30
SKYROOM_RETRY_TIMES=3
SKYROOM_RETRY_SLEEP=100
```

---

## Quick Start

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Enums\AccessLevel;

// 1. Create a room
$roomId = Skyroom::rooms()->create([
    'name' => 'physics-101',
    'title' => 'کلاس فیزیک',
    'guest_login' => false,
    'op_login_first' => true,
    'max_users' => 30,
]);

// 2. Generate direct instant login URL for a presenter
$loginUrl = Skyroom::createLoginUrl([
    'room_id' => $roomId,
    'user_id' => 'teacher-42',
    'nickname' => 'دکتر رضایی',
    'access' => AccessLevel::PRESENTER->value,
    'ttl' => 7200,
]);
```

---

## API Reference

### 1. Services

```php
// Get list of active and available Skyroom services
$services = Skyroom::services()->all();
// Or via shortcut:
$services = Skyroom::getServices();
```

### 2. Rooms

```php
use Skyroom\Laravel\Enums\AccessLevel;

// List rooms
$rooms = Skyroom::rooms()->all();

// Count rooms
$total = Skyroom::rooms()->count();

// Get room details by ID, name slug, or parameters
$room = Skyroom::rooms()->get(1);
$room = Skyroom::rooms()->get('physics-101');
$room = Skyroom::rooms()->get(['room_id' => 1]);

// Get room permanent URL
$url = Skyroom::rooms()->url(1, language: 'fa');

// Create room
$roomId = Skyroom::rooms()->create([
    'name' => 'math-exam',
    'title' => 'آزمون ریاضی',
    'guest_login' => false,
    'op_login_first' => true,
    'max_users' => 50,
]);

// Update room
Skyroom::rooms()->update($roomId, [
    'max_users' => 60,
    'title' => 'آزمون ریاضی (تمدید شده)',
]);

// Delete room
Skyroom::rooms()->delete($roomId);

// Get users with assigned room access
$users = Skyroom::rooms()->users($roomId);

// Add users to room
Skyroom::rooms()->addUsers($roomId, [
    ['user_id' => 10],
    ['user_id' => 20, 'access' => AccessLevel::PRESENTER->value],
]);

// Remove users from room
Skyroom::rooms()->removeUsers($roomId, [10, 20]);

// Update user access in room
Skyroom::rooms()->updateUser($roomId, userId: 10, access: AccessLevel::OPERATOR);
```

### 3. Users

```php
use Skyroom\Laravel\Enums\Status;
use Skyroom\Laravel\Enums\Gender;

// List users
$users = Skyroom::users()->all();

// Count users
$count = Skyroom::users()->count();

// Find user by ID or username
$user = Skyroom::users()->get(10);
$user = Skyroom::users()->get('student_ali');

// Create user
$userId = Skyroom::users()->create([
    'username' => 'student_ali',
    'password' => 'secret123',
    'nickname' => 'علی احمدی',
    'status' => Status::ACTIVE->value,
    'is_public' => false,
    'gender' => Gender::MALE->value,
]);

// Update user
Skyroom::users()->update($userId, [
    'nickname' => 'علی احمدی (نماینده کلاس)',
]);

// Delete single user
Skyroom::users()->delete($userId);

// Delete multiple users in batch
$result = Skyroom::users()->deleteMany([10, 11, 12]);
// ['success' => 2, 'failure' => 1]

// Get rooms assigned to user
$userRooms = Skyroom::users()->rooms($userId);

// Add rooms to user
Skyroom::users()->addRooms($userId, [
    ['room_id' => 1],
    ['room_id' => 2, 'access' => 2],
]);

// Remove rooms from user
Skyroom::users()->removeRooms($userId, [1, 2]);
```

### 4. Direct Login URL

Create temporary or direct single-sign-on (SSO) links into rooms without prompting user login credentials:

```php
use Skyroom\Laravel\Enums\AccessLevel;

$url = Skyroom::createLoginUrl([
    'room_id' => 1,
    'user_id' => 'crm-user-99',       // Identifier to prevent concurrent misuse
    'nickname' => 'محمد رضایی',
    'access' => AccessLevel::NORMAL->value, // 1: Normal, 2: Presenter, 3: Operator
    'concurrent' => 1,
    'language' => 'fa',
    'ttl' => 3600,                    // Time-to-live in seconds
]);
```

### 5. Raw API Calls

Call any custom or future action directly:

```php
$result = Skyroom::call('customAction', [
    'param_1' => 'value1',
]);
```

---

## Enums

Strongly typed PHP 8.2 enums:

```php
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Enums\Status;
use Skyroom\Laravel\Enums\Gender;
use Skyroom\Laravel\Enums\ErrorCode;

AccessLevel::NORMAL->value;    // 1 (کاربر عادی)
AccessLevel::PRESENTER->value; // 2 (ارایه دهنده)
AccessLevel::OPERATOR->value;  // 3 (اپراتور)

Status::INACTIVE->value;       // 0
Status::ACTIVE->value;         // 1

Gender::UNKNOWN->value;        // 0
Gender::MALE->value;           // 1
Gender::FEMALE->value;         // 2

ErrorCode::SERVICE_DISABLED->value; // 10
ErrorCode::INVALID_API_KEY->value;  // 11
ErrorCode::UNAUTHORIZED->value;     // 12
ErrorCode::INVALID_REQUEST->value;  // 13
ErrorCode::INTERNAL_ERROR->value;   // 14
ErrorCode::NOT_FOUND->value;        // 15
```

---

## Error Handling

When the Skyroom API returns an error response (`ok: false`), a specialized exception is thrown:

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Exceptions\NotFoundException;
use Skyroom\Laravel\Exceptions\ValidationException;
use Skyroom\Laravel\Exceptions\RateLimitException;
use Skyroom\Laravel\Exceptions\SkyroomApiException;

try {
    $room = Skyroom::rooms()->get(999999);
} catch (NotFoundException $e) {
    // Error code 15: Data not found
    echo $e->getMessage(); // داده مورد نظر پیدا نشد.
    echo $e->getErrorCode(); // 15
    echo $e->getAction(); // getRoom
} catch (ValidationException $e) {
    // Error code 13: Invalid request / parameters
} catch (RateLimitException $e) {
    // HTTP 503 rate limit exceeded (> 500 requests/minute)
} catch (SkyroomApiException $e) {
    // Other API errors (10, 11, 12, 14, ...)
}
```

---

## Testing & Faking

The package provides a built-in fake client that lets you test your application without sending actual network requests:

```php
use Skyroom\Laravel\Facades\Skyroom;

public function test_it_creates_skyroom_room(): void
{
    // 1. Fake responses
    Skyroom::fake([
        'createRoom' => 123,
    ]);

    // 2. Perform actions in your application
    $roomId = Skyroom::rooms()->create([
        'name' => 'physics-class',
        'title' => 'Physics Class',
    ]);

    // 3. Assert calls made
    Skyroom::assertSent('createRoom', function (array $params) {
        return $params['name'] === 'physics-class';
    });

    Skyroom::assertNotSent('deleteRoom');
    Skyroom::assertSentCount(1, 'createRoom');
}
```

You can also use Laravel's native `Http::fake()`:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://www.skyroom.online/skyroom/api/*' => Http::response([
        'ok' => true,
        'result' => [['id' => 1, 'name' => 'meeting']],
    ]),
]);
```

---

## Testing

Run unit & feature tests:

```bash
composer test
```

Run static analysis:

```bash
composer analyse
```

---

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
