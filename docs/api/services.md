# Services API

The `ServicesResource` retrieves your active Skyroom subscriptions, concurrent user quotas, webcam limits, and traffic usage.

```php
use Skyroom\Laravel\Facades\Skyroom;
```

---

## `all()`

Returns a list of all active and expired service packages in your account.

```php
$services = Skyroom::services()->all();
// Or:
$services = Skyroom::getServices();
```

**Response Format:**
```php
[
    [
        'id' => 1,
        'title' => 'سرویس وب کنفرانس',
        'status' => 1,          // 0: Inactive, 1: Active
        'user_limit' => 50,     // Max concurrent users online
        'video_limit' => 8,     // Max concurrent webcams active
        'time_limit' => null,   // Usage quota limit (seconds)
        'time_usage' => 2213762,// Used quota in person-seconds
        'start_time' => 1582851460,
        'stop_time' => 1580259460,
        'create_time' => 1582851460,
        'update_time' => 1580259460,
    ],
]
```

---

## Service Properties Explained

| Field | Type | Description |
| :--- | :--- | :--- |
| `id` | `int` | Unique service subscription ID. |
| `title` | `string` | Service name (e.g., "سرویس وب کنفرانس"). |
| `status` | `int` | `1` if the subscription is currently active, `0` if expired/inactive. |
| `user_limit` | `int` | Maximum simultaneous online participants across the account. |
| `video_limit` | `int` | Maximum simultaneous cameras allowed per room. |
| `time_limit` | `int\|null` | Total allocated person-seconds, or `null` for unlimited. |
| `time_usage` | `int` | Consumed person-seconds. |
| `start_time` | `int` | Subscription start timestamp (Unix epoch). |
| `stop_time` | `int` | Subscription expiration timestamp (Unix epoch). |

::: tip Room Creation & Multiple Services
When creating a room, if your account possesses more than one active service subscription, you can explicitly designate `service_id` in your room configuration. Otherwise, Skyroom defaults to the active service.
:::
