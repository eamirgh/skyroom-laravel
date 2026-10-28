# Enums

The package ships with four strongly-typed PHP 8.2+ enums for clean, type-safe development.

---

## `AccessLevel`

Namespace: `Skyroom\Laravel\Enums\AccessLevel`

Defines participant roles and privileges inside a Skyroom room.

```php
use Skyroom\Laravel\Enums\AccessLevel;

AccessLevel::NORMAL->value;    // 1
AccessLevel::PRESENTER->value; // 2
AccessLevel::OPERATOR->value;  // 3

// Human-readable Persian description:
AccessLevel::NORMAL->description();    // 'کاربر عادی'
AccessLevel::PRESENTER->description(); // 'ارایه دهنده'
AccessLevel::OPERATOR->description();  // 'اپراتور'
```

| Enum Case | Value | Persian Label | Privileges |
| :--- | :---: | :--- | :--- |
| `AccessLevel::NORMAL` | `1` | کاربر عادی | Student / attendee: can chat, watch presentation, request microphone. |
| `AccessLevel::PRESENTER` | `2` | ارایه دهنده | Speaker / teacher: can share webcam, mic, desktop screen, whiteboard, and slides. |
| `AccessLevel::OPERATOR` | `3` | اپراتور | Host / room admin: full administrative control, can kick users, record room, manage layout. |

---

## `Status`

Namespace: `Skyroom\Laravel\Enums\Status`

Represents active or inactive status for users, services, and rooms.

```php
use Skyroom\Laravel\Enums\Status;

Status::INACTIVE->value; // 0
Status::ACTIVE->value;   // 1

Status::ACTIVE->description();   // 'فعال'
Status::INACTIVE->description(); // 'غیرفعال'
```

---

## `Gender`

Namespace: `Skyroom\Laravel\Enums\Gender`

Represents user gender in Skyroom profiles.

```php
use Skyroom\Laravel\Enums\Gender;

Gender::UNKNOWN->value; // 0
Gender::MALE->value;    // 1
Gender::FEMALE->value;  // 2

Gender::MALE->description();   // 'مرد'
Gender::FEMALE->description(); // 'زن'
Gender::UNKNOWN->description();// 'نامعلوم'
```

---

## `ErrorCode`

Namespace: `Skyroom\Laravel\Enums\ErrorCode`

Represents error codes returned by the Skyroom Web Service.

```php
use Skyroom\Laravel\Enums\ErrorCode;

ErrorCode::SERVICE_DISABLED->value; // 10
ErrorCode::INVALID_API_KEY->value;  // 11
ErrorCode::UNAUTHORIZED->value;     // 12
ErrorCode::INVALID_REQUEST->value;  // 13
ErrorCode::INTERNAL_ERROR->value;   // 14
ErrorCode::NOT_FOUND->value;        // 15

ErrorCode::NOT_FOUND->description(); // 'داده مورد نظر پیدا نشد.'
```
