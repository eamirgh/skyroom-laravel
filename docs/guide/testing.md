# Testing & Mocking

`skyroom-laravel` provides first-class testing utilities with `Skyroom::fake()`, allowing you to write fast, isolated tests without issuing actual HTTP calls to Skyroom.

---

## Using `Skyroom::fake()`

Call `Skyroom::fake()` at the beginning of your test:

```php
use Skyroom\Laravel\Facades\Skyroom;
use Tests\TestCase;

class ClassroomTest extends TestCase
{
    public function test_creates_classroom_successfully(): void
    {
        // 1. Fake Skyroom responses
        Skyroom::fake([
            'createRoom' => 101,
            'createLoginUrl' => 'https://www.skyroom.online/ch/room/fake-login-url',
        ]);

        // 2. Run your application code
        $response = $this->postJson('/api/classes', [
            'name' => 'physics-class',
            'title' => 'Physics Class',
        ]);

        $response->assertStatus(201);

        // 3. Assert Skyroom interactions
        Skyroom::assertSent('createRoom', function (array $params) {
            return $params['name'] === 'physics-class'
                && $params['title'] === 'Physics Class';
        });

        Skyroom::assertNotSent('deleteRoom');
        Skyroom::assertSentCount(1, 'createRoom');
    }
}
```

---

## Dynamic Responses with Callables

You can supply closures to return dynamic fake responses based on the request parameters:

```php
Skyroom::fake([
    'getRoom' => function (array $params) {
        if (($params['name'] ?? null) === 'existing-room') {
            return ['id' => 50, 'name' => 'existing-room', 'title' => 'Existing Room'];
        }

        throw new \Skyroom\Laravel\Exceptions\NotFoundException('Room not found', 15);
    },
]);
```

---

## Default Fake Fallbacks

If you call `Skyroom::fake()` without arguments, sensible default responses are provided:

- `countRooms`, `countUsers` => `1`
- `createRoom`, `createUser` => `1`
- `updateRoom`, `updateUser`, `updateRoomUser` => `1`
- `deleteRoom`, `deleteUser` => `1`
- `deleteUsers` => `['success' => 1, 'failure' => 0]`
- `getRooms`, `getUsers`, `getServices`, `getRoomUsers`, `getUserRooms` => `[]`
- `getRoomUrl`, `createLoginUrl` => `'https://www.skyroom.online/ch/room/fake-token'`

---

## Available Assertions

| Method | Description |
| :--- | :--- |
| `Skyroom::assertSent(string $action, ?callable $callback = null)` | Asserts that `$action` was called, optionally verifying parameters with a callback. |
| `Skyroom::assertNotSent(string $action, ?callable $callback = null)` | Asserts that `$action` was never invoked. |
| `Skyroom::assertNothingSent()` | Asserts that zero actions were called on Skyroom. |
| `Skyroom::assertSentCount(int $count, ?string $action = null)` | Asserts total actions sent, or count of a specific action. |

---

## Native `Http::fake()` Compatibility

Because `SkyroomClient` uses Laravel's HTTP Client (`Http::class`), you can also mock responses with Laravel's native `Http::fake()`:

```php
use Illuminate\Support\Facades\Http;

Http::fake([
    'https://www.skyroom.online/skyroom/api/*' => Http::response([
        'ok' => true,
        'result' => [
            ['id' => 1, 'name' => 'room-1', 'title' => 'Room 1'],
        ],
    ], 200),
]);
```
