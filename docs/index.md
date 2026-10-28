---
layout: home

hero:
  name: "Skyroom Laravel"
  text: "Production-ready Skyroom API wrapper"
  tagline: "Fluent, typed, resilient Laravel wrapper with built-in testing mocks and 100% Skyroom API coverage."
  actions:
    - theme: brand
      text: Get Started
      link: /guide/getting-started
    - theme: alt
      text: API Reference
      link: /api/overview
    - theme: alt
      text: Real-World Examples
      link: /examples/school-lms

features:
  - title: 100% Skyroom API Coverage
    details: Complete implementation of Rooms, Users, Services, and Direct Login URLs matching official Skyroom documentation.
  - title: Strongly Typed PHP 8.2+
    details: First-class PHP enums for access levels, user status, gender, and error codes. Full IDE autocompletion and static analysis.
  - title: Built-in Testing & Fakes
    details: Built-in `Skyroom::fake()` with `assertSent`, `assertNotSent`, and `assertSentCount` helpers. Zero real HTTP requests during testing.
  - title: Automatic Resilience
    details: Automatic retries with backoff on network dropouts and HTTP 503 rate-limit responses.
  - title: Laravel 10, 11, 12 & 13
    details: Fully tested and compatible across modern Laravel versions with auto-discovery and config publishing.
  - title: Expressive Fluent API
    details: Access via `Skyroom::rooms()->create(...)` or clean direct shortcuts `Skyroom::createRoom(...)`.
---

## Quick Look

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Enums\AccessLevel;

// 1. Create a virtual classroom
$roomId = Skyroom::rooms()->create([
    'name' => 'physics-101',
    'title' => 'کلاس فیزیک پایه',
    'guest_login' => false,
    'op_login_first' => true,
    'max_users' => 50,
]);

// 2. Generate direct instant SSO login URL for the teacher
$teacherUrl = Skyroom::createLoginUrl([
    'room_id' => $roomId,
    'user_id' => 'teacher_42',
    'nickname' => 'دکتر رضایی',
    'access' => AccessLevel::OPERATOR->value,
    'ttl' => 7200,
]);

// 3. Generate direct instant SSO login URL for a student
$studentUrl = Skyroom::createLoginUrl([
    'room_id' => $roomId,
    'user_id' => 'student_108',
    'nickname' => 'سارا احمدی',
    'access' => AccessLevel::NORMAL->value,
    'ttl' => 7200,
]);
```
