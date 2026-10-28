# Rooms API

The `RoomsResource` manages virtual classrooms, meetings, and webinars.

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Enums\AccessLevel;
```

---

## `all()`

Returns a list of all existing rooms.

```php
$rooms = Skyroom::rooms()->all();
// Or:
$rooms = Skyroom::getRooms();
```

**Response Format:**
```php
[
    [
        'id' => 1,
        'service_id' => 8230,
        'name' => 'meeting',
        'title' => 'اتاق جلسات',
        'status' => 1,
    ],
    // ...
]
```

---

## `count()`

Returns the total count of rooms.

```php
$count = Skyroom::rooms()->count();
// Or:
$count = Skyroom::countRooms();
```

---

## `get(int|string|array $idOrName)`

Retrieves full details of a specific room. Accepts a numeric Room ID, string name slug, or array of parameters.

```php
// By Room ID
$room = Skyroom::rooms()->get(1);

// By unique Room Name slug
$room = Skyroom::rooms()->get('physics-101');

// By parameters array
$room = Skyroom::rooms()->get(['room_id' => 1]);
```

**Response Fields:**
```php
[
    'id' => 1,
    'service_id' => 8230,
    'name' => 'physics-101',
    'title' => 'کلاس فیزیک',
    'description' => 'توضیحات کلاس',
    'status' => 1,
    'guest_login' => false,
    'guest_limit' => 0,
    'op_login_first' => true,
    'max_users' => 30,
    'session_duration' => null,
    'time_limit' => null,
    'time_usage' => 184486,
    'time_total' => 3144460,
    'create_time' => 1479021434,
    'update_time' => 1501559112,
]
```

---

## `url(int $roomId, string $language = 'fa')`

Returns the permanent public URL to join a room.

```php
$url = Skyroom::rooms()->url(1, language: 'fa');
// Or:
$url = Skyroom::getRoomUrl(1, 'fa');

// Returns: "https://www.skyroom.online/ch/your-subdomain/physics-101"
```

---

## `create(array $params)`

Creates a new room. Returns the newly created `room_id` integer.

```php
$roomId = Skyroom::rooms()->create([
    'name' => 'math-exercise',         // Latin name slug (max 128 chars)
    'title' => 'کلاس حل تمرین ریاضی',    // Display title (max 128 chars)
    'description' => 'شرح دوره ریاضی', // Optional description (max 512 chars)
    'guest_login' => false,             // Allow guests without accounts
    'guest_limit' => 0,                 // 0 = unlimited guests up to max_users
    'op_login_first' => true,           // Require operator to enter first
    'max_users' => 25,                  // Maximum concurrent users
    'session_duration' => 90,           // Max session minutes (optional)
]);
```

---

## `update(int $roomId, array $params)`

Updates existing room properties. Returns `1` on success.

```php
Skyroom::rooms()->update($roomId, [
    'title' => 'کلاس فیزیک پیشرفته',
    'max_users' => 40,
    'guest_login' => true,
]);
```

---

## `delete(int $roomId)`

Deletes a room permanently. Returns `1` on success.

```php
Skyroom::rooms()->delete($roomId);
```

---

## `users(int $roomId)`

Lists users who have been granted access to this room.

```php
$users = Skyroom::rooms()->users($roomId);
```

**Response Format:**
```php
[
    [
        'user_id' => 6344,
        'username' => 'operator',
        'nickname' => 'اپراتور کلاس',
        'access' => 3, // 1: Normal, 2: Presenter, 3: Operator
    ],
]
```

---

## `addUsers(int $roomId, array $users)`

Grants multiple users access to a room. Returns the number of users added.

```php
use Skyroom\Laravel\Enums\AccessLevel;

$added = Skyroom::rooms()->addUsers($roomId, [
    ['user_id' => 101],                                         // Default access (Normal: 1)
    ['user_id' => 102, 'access' => AccessLevel::PRESENTER->value], // Presenter (2)
    ['user_id' => 103, 'access' => AccessLevel::OPERATOR->value],  // Operator (3)
]);
```

---

## `removeUsers(int $roomId, array $users)`

Revokes room access from users. Accepts an array of user IDs. Returns the count of removed users.

```php
$removed = Skyroom::rooms()->removeUsers($roomId, [101, 102]);
```

---

## `updateUser(int $roomId, int $userId, AccessLevel|int $access)`

Updates the access level for a specific user in a room. Accepts either an `AccessLevel` enum or integer (`1`, `2`, `3`).

```php
use Skyroom\Laravel\Enums\AccessLevel;

Skyroom::rooms()->updateUser($roomId, userId: 101, access: AccessLevel::OPERATOR);
```
