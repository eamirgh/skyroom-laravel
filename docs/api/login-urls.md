# Login URLs (Direct SSO)

Generating temporary direct login URLs allows your users to enter Skyroom classrooms or webinars **without creating Skyroom user accounts or typing credentials**.

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Enums\AccessLevel;
```

---

## `createLoginUrl(array $params)`

Generates a secure, temporary, single-sign-on (SSO) URL for a participant.

```php
$url = Skyroom::createLoginUrl([
    'room_id' => 1,                          // Required: target Room ID
    'user_id' => 'crm_student_95',           // Required: unique user ID to prevent link sharing
    'nickname' => 'امیر رضایی',             // Required: participant display name in the room
    'access' => AccessLevel::NORMAL->value,  // Required: 1: Normal, 2: Presenter, 3: Operator
    'concurrent' => 1,                       // Max concurrent entries with this link (default: 1)
    'language' => 'fa',                      // Interface language ('fa' or 'en')
    'ttl' => 3600,                           // Validity duration in seconds (default: 3600)
]);
```

**Returned URL Structure:**
```
https://www.skyroom.online/ch/your-subdomain/room-name/t/eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

---

## Parameter Specifications

| Parameter | Type | Required | Description |
| :--- | :---: | :---: | :--- |
| `room_id` | `int` | **Yes** | The ID of the room to join. |
| `user_id` | `string\|int` | **Yes** | Unique identifier from your database (e.g., your application's user ID). Prevents participants from sharing the same link. |
| `nickname` | `string` | **Yes** | The display name visible in the participant list and chat. |
| `access` | `int` | **Yes** | Participant role: `1` (Normal), `2` (Presenter), `3` (Operator). |
| `concurrent` | `int` | No | Number of simultaneous entries allowed using this link. Default is `1`. Set higher only for shared group links. |
| `language` | `string` | No | Skyroom UI language: `'fa'` (Persian, default) or `'en'` (English). |
| `ttl` | `int` | No | Link time-to-live in seconds. Default is `3600` (1 hour). After this time, unused links expire. |

---

## Best Practice: Managing Rate Limits

::: warning Avoid creating links in bulk at the same second!
If hundreds of students join simultaneous classes at 8:00 AM, generating hundreds of `createLoginUrl` requests in a loop will encounter the **500 requests/minute** limit.
:::

**Recommended Solution:**
1. Generate direct login URLs **in advance** when scheduling or publishing the class session.
2. Store the generated link in your database (`class_sessions.skyroom_url`).
3. Set `ttl` according to your class window (e.g., `ttl => 86400` for 24 hours, or duration of the session).
4. Direct students to the stored URL when they click "Join Class".
