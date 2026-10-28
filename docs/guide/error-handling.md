# Error Handling

The Skyroom API always returns an HTTP `200 OK` status for programmatic requests, returning errors in the JSON body:

```json
{
  "ok": false,
  "error_code": 15,
  "error_message": "داده مورد نظر پیدا نشد."
}
```

The package intercepts these responses and translates them into strongly-typed domain exceptions.

---

## Exception Hierarchy

All exceptions extend the base `Skyroom\Laravel\Exceptions\SkyroomException`:

```
SkyroomException (RuntimeException)
├── RateLimitException (HTTP 503 rate-limit or outage)
├── NetworkException (cURL/DNS/SSL connection error)
└── SkyroomApiException (API response with ok: false)
    ├── ServiceDisabledException (Code 10)
    ├── AuthenticationException  (Code 11)
    ├── UnauthorizedException    (Code 12)
    ├── ValidationException      (Code 13)
    ├── ServerException          (Code 14)
    └── NotFoundException        (Code 15)
```

---

## Error Code Reference

| Code | Exception Class | Description (Persian) | When it happens |
| :---: | :--- | :--- | :--- |
| `10` | `ServiceDisabledException` | وب‌سرویس غیرفعال است. | Skyroom service or API key is deactivated. |
| `11` | `AuthenticationException` | کلید وب‌سرویس نامعتبر است. | API key is invalid, missing, or malformed. |
| `12` | `UnauthorizedException` | درخواست شما غیرمجاز است. | Account lacks permissions for the action. |
| `13` | `ValidationException` | درخواست شما معتبر نیست. | Missing required parameters or invalid values. |
| `14` | `ServerException` | درخواست شما با خطا روبرو شد. | Skyroom internal server error occurred. |
| `15` | `NotFoundException` | داده مورد نظر پیدا نشد. | Specified Room ID, User ID, or Name does not exist. |

---

## Catching Specific Errors

You can catch specific exceptions to handle domain logic gracefully:

```php
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Exceptions\NotFoundException;
use Skyroom\Laravel\Exceptions\ValidationException;
use Skyroom\Laravel\Exceptions\RateLimitException;
use Skyroom\Laravel\Exceptions\SkyroomApiException;

try {
    $room = Skyroom::rooms()->get('math-class-2026');
} catch (NotFoundException $e) {
    // Room doesn't exist yet -> create it
    $roomId = Skyroom::rooms()->create([
        'name' => 'math-class-2026',
        'title' => 'Math Class 2026',
    ]);
} catch (ValidationException $e) {
    // Invalid parameters
    Log::warning('Validation failed for Skyroom parameters', [
        'params' => $e->getParams(),
        'message' => $e->getMessage(),
    ]);
} catch (RateLimitException $e) {
    // 503 rate limit exceeded
    // Queue the job to retry after backoff
} catch (SkyroomApiException $e) {
    // General API error
    Log::error('Skyroom API error: ' . $e->getMessage(), [
        'code' => $e->getErrorCode(),
        'action' => $e->getAction(),
    ]);
}
```

---

## Exception Methods

`SkyroomApiException` provides inspection methods:

```php
$e->getErrorCode(); // int: error_code (e.g., 15)
$e->getMessage();   // string: error_message in Persian (e.g. "داده مورد نظر پیدا نشد.")
$e->getAction();    // string: API action invoked (e.g., "getRoom")
$e->getParams();    // array: parameters sent with the request
```
