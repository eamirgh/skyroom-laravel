# School & LMS Integration

This guide walks through a production architecture for integrating Skyroom with a School or University Learning Management System (LMS).

---

## Architecture Plan

1. **Classrooms are created once** per course (e.g., `course_id: 101`).
2. The Skyroom `room_id` is stored in your `courses` table.
3. When a scheduled live class session starts, generate **direct login URLs** dynamically for the enrolled students and instructor.
4. Students click a single button: "Join Class" and enter immediately.

---

## 1. Database Migration

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedBigInteger('skyroom_room_id')->nullable()->after('title');
            $table->string('skyroom_room_name', 128)->nullable()->after('skyroom_room_id');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['skyroom_room_id', 'skyroom_room_name']);
        });
    }
};
```

---

## 2. Course Service

Create or find a Skyroom room whenever a new course is created in your application:

```php
namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Str;
use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Exceptions\NotFoundException;

class CourseLiveClassService
{
    /**
     * Ensure a course has an associated Skyroom room.
     */
    public function ensureRoomExists(Course $course): int
    {
        if ($course->skyroom_room_id) {
            return $course->skyroom_room_id;
        }

        $slug = 'course-' . $course->id . '-' . Str::slug($course->slug, '-');

        try {
            // Check if room slug already exists
            $existing = Skyroom::rooms()->get($slug);
            $roomId = $existing['id'];
        } catch (NotFoundException) {
            // Create the room
            $roomId = Skyroom::rooms()->create([
                'name' => $slug,
                'title' => $course->title,
                'description' => 'کلاس آنلاین ' . $course->title,
                'guest_login' => false,
                'op_login_first' => true, // Wait until teacher enters
                'max_users' => $course->capacity ?? 50,
            ]);
        }

        $course->update([
            'skyroom_room_id' => $roomId,
            'skyroom_room_name' => $slug,
        ]);

        return $roomId;
    }
}
```

---

## 3. Joining Controller

When students and teachers click "Join Live Class", generate a signed login link according to their role:

```php
namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Facades\Skyroom;

class CourseLiveSessionController extends Controller
{
    public function join(Request $request, Course $course): RedirectResponse
    {
        $user = $request->user();

        // 1. Check enrollment authorization
        abort_unless($user->isEnrolledIn($course) || $user->isInstructorOf($course), 403);

        // 2. Ensure room exists
        $roomId = app(\App\Services\CourseLiveClassService::class)->ensureRoomExists($course);

        // 3. Determine permissions (Operator for instructor, Normal for student)
        $access = $user->isInstructorOf($course)
            ? AccessLevel::OPERATOR
            : AccessLevel::NORMAL;

        // 4. Generate temporary 3-hour SSO login URL
        $joinUrl = Skyroom::createLoginUrl([
            'room_id' => $roomId,
            'user_id' => 'lms_user_' . $user->id,
            'nickname' => $user->name,
            'access' => $access->value,
            'concurrent' => 1,
            'language' => 'fa',
            'ttl' => 10800, // 3 hours
        ]);

        return redirect()->away($joinUrl);
    }
}
```
