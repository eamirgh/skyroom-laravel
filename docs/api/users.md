# Users API

The `UsersResource` manages registered user accounts on your Skyroom domain.

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Enums\Status;
use Skyroom\Laravel\Enums\Gender;
```

---

## `all()`

Returns a list of all user accounts.

```php
$users = Skyroom::users()->all();
// Or:
$users = Skyroom::getUsers();
```

**Response Format:**
```php
[
    [
        'id' => 1,
        'username' => 'parham',
        'nickname' => 'پرهام',
        'status' => 1,
    ],
    // ...
]
```

---

## `count()`

Returns the total count of registered users.

```php
$count = Skyroom::users()->count();
```

---

## `get(int|string|array $idOrUsername)`

Retrieves details for a specific user by ID, username, or params.

```php
// By User ID
$user = Skyroom::users()->get(1);

// By Username
$user = Skyroom::users()->get('parham');

// By parameters array
$user = Skyroom::users()->get(['user_id' => 1]);
```

**Response Fields:**
```php
[
    'id' => 1,
    'username' => 'parham',
    'nickname' => 'پرهام',
    'email' => 'example@gmail.com',
    'fname' => 'پرهام',
    'lname' => 'عابدینی',
    'gender' => 1, // 0: Unknown, 1: Male, 2: Female
    'status' => 1, // 0: Inactive, 1: Active
    'is_public' => false,
    'concurrent' => 0, // 0 = unlimited concurrent logins
    'time_limit' => null,
    'time_usage' => 426466,
    'time_total' => 8464516,
    'expiry_date' => null,
    'create_time' => 1489021434,
    'update_time' => 1489021434,
]
```

---

## `create(array $params)`

Creates a new user. Returns the created `user_id` integer.

```php
use Skyroom\Laravel\Enums\Status;
use Skyroom\Laravel\Enums\Gender;

$userId = Skyroom::users()->create([
    'username' => 'ali_rezaei',         // Latin characters (max 32 chars)
    'password' => 'SecurePass123!',     // Max 24 chars
    'nickname' => 'علی رضایی',          // Display name (max 128 chars)
    'email' => 'ali@example.com',       // Optional (max 128 chars)
    'fname' => 'علی',                   // Optional first name
    'lname' => 'رضایی',                 // Optional last name
    'gender' => Gender::MALE->value,    // 0: Unknown, 1: Male, 2: Female
    'status' => Status::ACTIVE->value,  // 0: Inactive, 1: Active
    'is_public' => false,               // True: public account for shared access
    'concurrent' => 1,                  // Max concurrent sessions (0 = unlimited)
]);
```

---

## `update(int $userId, array $params)`

Updates an existing user profile or credentials. Returns `1` on success.

```php
Skyroom::users()->update($userId, [
    'nickname' => 'علی رضایی (سرگروه)',
    'password' => 'NewSecretPassword!',
    'status' => Status::ACTIVE->value,
]);
```

---

## `delete(int $userId)`

Deletes a single user account permanently. Returns `1` on success.

```php
Skyroom::users()->delete($userId);
```

---

## `deleteMany(array $users)`

Deletes multiple users in one call. Accepts an array of user IDs.

```php
$result = Skyroom::users()->deleteMany([101, 102, 103]);

// Returns:
// [
//     'success' => 2,
//     'failure' => 1,
// ]
```

---

## `rooms(int $userId)`

Lists all rooms that a user has access to.

```php
$rooms = Skyroom::users()->rooms($userId);
```

**Response Format:**
```php
[
    [
        'room_id' => 1,
        'name' => 'meeting',
        'title' => 'اتاق جلسات',
        'access' => 1,
    ],
]
```

---

## `addRooms(int $userId, array $rooms)`

Assigns multiple rooms to a user. Returns count of assigned rooms.

```php
use Skyroom\Laravel\Enums\AccessLevel;

$count = Skyroom::users()->addRooms($userId, [
    ['room_id' => 1],
    ['room_id' => 2, 'access' => AccessLevel::PRESENTER->value],
]);
```

---

## `removeRooms(int $userId, array $rooms)`

Removes room assignments from a user. Accepts an array of room IDs.

```php
$count = Skyroom::users()->removeRooms($userId, [1, 2]);
```
