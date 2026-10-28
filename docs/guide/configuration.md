# Configuration

The published `config/skyroom.php` configuration file controls all aspects of the client behavior, connection timeouts, and automatic retry policies.

---

## Configuration File

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Skyroom API Key
    |--------------------------------------------------------------------------
    |
    | Your 50-character Skyroom API key obtained from Skyroom dashboard.
    |
    */
    'api_key' => env('SKYROOM_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Skyroom Base API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Skyroom web service.
    |
    */
    'base_url' => env('SKYROOM_BASE_URL', 'https://www.skyroom.online/skyroom/api/'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout in seconds for HTTP requests sent to Skyroom API.
    |
    */
    'timeout' => (int) env('SKYROOM_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Automatic retry configuration for transient network/server failures.
    |
    */
    'retry' => [
        'times' => (int) env('SKYROOM_RETRY_TIMES', 3),
        'sleep_milliseconds' => (int) env('SKYROOM_RETRY_SLEEP', 100),
    ],
];
```

---

## Options Breakdown

### `api_key`
- **Type**: `string`
- **Default**: `env('SKYROOM_API_KEY', '')`
- The 50-character unique API key assigned to your Skyroom account. If left empty, any API call will throw `Skyroom\Laravel\Exceptions\AuthenticationException`.

### `base_url`
- **Type**: `string`
- **Default**: `'https://www.skyroom.online/skyroom/api/'`
- The endpoint root. The client automatically appends the API key to form:
  `https://www.skyroom.online/skyroom/api/{api_key}`

### `timeout`
- **Type**: `int` (seconds)
- **Default**: `30`
- Sets HTTP socket and connection timeout for all outgoing requests. Prevents your application from hanging when network issues occur.

### `retry`
- **Type**: `array`
  - `times`: Number of attempts (default: `3`)
  - `sleep_milliseconds`: Delay in milliseconds between retry attempts (default: `100`)
- Skyroom limits API requests to **500 requests per minute**. When the limit is temporarily exceeded, Skyroom returns an HTTP `503 Service Unavailable` response. The built-in retry mechanism automatically retries transient connection exceptions and 503 responses before giving up and throwing a `RateLimitException`.
