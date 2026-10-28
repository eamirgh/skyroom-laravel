# Webinar & Event Platform

This example demonstrates managing public or paid webinars with operator hosts, guest access, and paid attendee ticket verification.

---

## Scenario Overview

- A company hosts a paid 2-hour technical webinar.
- 3 speakers need presenter rights (`AccessLevel::PRESENTER`).
- 1 host needs operator rights (`AccessLevel::OPERATOR`).
- Attendees who purchased a ticket get a private single-use login link.
- Unregistered users cannot enter.

---

## 1. Creating the Webinar Room

```php
namespace App\Services;

use App\Models\Webinar;
use Skyroom\Laravel\Facades\Skyroom;

class WebinarManager
{
    public function setupWebinarRoom(Webinar $webinar): int
    {
        $roomSlug = 'webinar-' . $webinar->id;

        return Skyroom::rooms()->create([
            'name' => $roomSlug,
            'title' => $webinar->title,
            'description' => $webinar->summary,
            'guest_login' => false,       // Enforce ticket holders only
            'op_login_first' => true,     // Participants wait until host starts
            'max_users' => $webinar->max_attendees ?? 200,
            'session_duration' => 150,    // 2.5 hours maximum session
        ]);
    }
}
```

---

## 2. Generating Ticket Links on Purchase

When an attendee finishes checkout, generate their personal entry link and store it on their `Ticket` model:

```php
namespace App\Listeners;

use App\Events\TicketPurchased;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Facades\Skyroom;

class GenerateWebinarEntryLink
{
    public function handle(TicketPurchased $event): void
    {
        $ticket = $event->ticket;
        $webinar = $ticket->webinar;
        $attendee = $ticket->user;

        // Calculate TTL until webinar end time + 2 hours buffer
        $ttlSeconds = max(
            3600,
            now()->diffInSeconds($webinar->starts_at->addHours(4))
        );

        $loginUrl = Skyroom::createLoginUrl([
            'room_id' => $webinar->skyroom_room_id,
            'user_id' => 'ticket_' . $ticket->uuid,
            'nickname' => $attendee->name,
            'access' => AccessLevel::NORMAL->value,
            'concurrent' => 1, // Prevent sharing ticket link with other people
            'language' => 'fa',
            'ttl' => $ttlSeconds,
        ]);

        $ticket->update(['skyroom_entry_url' => $loginUrl]);

        // Email or SMS ticket to attendee...
    }
}
```

---

## 3. Host and Speaker Entry

Hosts and speakers use their own management dashboard:

```php
namespace App\Http\Controllers;

use App\Models\Webinar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Facades\Skyroom;

class WebinarHostController extends Controller
{
    public function enterAsHost(Request $request, Webinar $webinar): RedirectResponse
    {
        abort_unless($webinar->isHostedBy($request->user()), 403);

        $url = Skyroom::createLoginUrl([
            'room_id' => $webinar->skyroom_room_id,
            'user_id' => 'host_' . $request->user()->id,
            'nickname' => $request->user()->name . ' (میزبان وبینار)',
            'access' => AccessLevel::OPERATOR->value,
            'concurrent' => 1,
            'language' => 'fa',
            'ttl' => 7200,
        ]);

        return redirect()->away($url);
    }
}
```
