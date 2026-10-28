# Architecture & Dependency Injection

`skyroom-laravel` is architected for clean separation of concerns, testability, and adherence to modern Laravel design patterns.

---

## Service Container Binding

The client is registered in the Laravel service container as a **singleton**:

- Contract: `Skyroom\Laravel\Contracts\SkyroomClientContract`
- Implementation: `Skyroom\Laravel\SkyroomClient`
- Aliases: `skyroom`

```php
use Skyroom\Laravel\Contracts\SkyroomClientContract;
use Skyroom\Laravel\SkyroomClient;

// All resolve the same singleton instance
$client1 = app(SkyroomClientContract::class);
$client2 = app(SkyroomClient::class);
$client3 = app('skyroom');
```

---

## Dependency Injection Example

In your controllers, jobs, or domain service classes, type-hint `SkyroomClientContract`:

```php
namespace App\Services;

use Skyroom\Laravel\Contracts\SkyroomClientContract;
use Skyroom\Laravel\Enums\AccessLevel;

class ClassroomManager
{
    public function __construct(
        protected SkyroomClientContract $skyroom,
    ) {}

    public function startClass(string $roomName, string $teacherId): string
    {
        $room = $this->skyroom->rooms()->get($roomName);

        return $this->skyroom->createLoginUrl([
            'room_id' => $room['id'],
            'user_id' => $teacherId,
            'nickname' => 'Instructor',
            'access' => AccessLevel::OPERATOR->value,
            'ttl' => 3600,
        ]);
    }
}
```

---

## Facade Usage

For rapid development and procedural contexts, use the `Skyroom` facade:

```php
use Skyroom\Laravel\Facades\Skyroom;

// Access resource managers:
$rooms = Skyroom::rooms()->all();
$users = Skyroom::users()->all();
$services = Skyroom::services()->all();

// Or use direct shortcut helpers:
$rooms = Skyroom::getRooms();
$users = Skyroom::getUsers();
$services = Skyroom::getServices();
```

---

## Resource Modules

API calls are grouped into three distinct resource classes:

1. **`RoomsResource` (`Skyroom::rooms()`):**
   Handles room creation, listing, updating, deletion, user permission mapping, and room URL generation.
2. **`UsersResource` (`Skyroom::users()`):**
   Handles user accounts creation, profile updates, batch deletions, and assigned rooms.
3. **`ServicesResource` (`Skyroom::services()`):**
   Retrieves available active subscriptions and concurrency quotas.
