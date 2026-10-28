# Bulk User Synchronization

This guide covers batch operations: importing users from a local database into Skyroom, batch deletions, and keeping state synchronized.

---

## 1. Bulk Sync Artisan Command

A command to sync registered staff or students to Skyroom user accounts:

```php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Skyroom\Laravel\Enums\Status;
use Skyroom\Laravel\Exceptions\ValidationException;
use Skyroom\Laravel\Facades\Skyroom;

class SyncSkyroomUsersCommand extends Command
{
    protected $signature = 'skyroom:sync-users {--chunk=50}';
    protected $description = 'Sync database users to Skyroom accounts';

    public function handle(): int
    {
        $this->info('Starting user synchronization...');

        User::whereNull('skyroom_user_id')
            ->chunkById((int) $this->option('chunk'), function ($users) {
                foreach ($users as $user) {
                    try {
                        $skyroomUserId = Skyroom::users()->create([
                            'username' => 'u_' . $user->id,
                            'password' => $user->temporary_password ?? Str::random(12),
                            'nickname' => $user->name,
                            'email' => $user->email,
                            'status' => Status::ACTIVE->value,
                            'is_public' => false,
                        ]);

                        $user->update(['skyroom_user_id' => $skyroomUserId]);
                        $this->line("Synced: {$user->email} (Skyroom ID: {$skyroomUserId})");
                    } catch (ValidationException $e) {
                        $this->warn("Skipped {$user->email}: " . $e->getMessage());
                    }

                    // Respect Skyroom rate limit (500 req / minute = max ~8 req / sec)
                    usleep(150000); // 150ms delay
                }
            });

        $this->info('Synchronization complete.');

        return Command::SUCCESS;
    }
}
```

---

## 2. Bulk Deletion

When an academic year concludes or students graduate, you can purge accounts in bulk using `deleteMany()`:

```php
namespace App\Services;

use App\Models\Student;
use Skyroom\Laravel\Facades\Skyroom;

class SkyroomCleanupService
{
    public function purgeGraduatedStudents(): array
    {
        // 1. Gather Skyroom IDs of graduated students
        $graduatedIds = Student::where('graduated', true)
            ->whereNotNull('skyroom_user_id')
            ->pluck('skyroom_user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($graduatedIds)) {
            return ['deleted' => 0];
        }

        $totalSuccess = 0;
        $totalFailure = 0;

        // 2. Chunk in batches of 50
        foreach (array_chunk($graduatedIds, 50) as $batch) {
            $result = Skyroom::users()->deleteMany($batch);

            $totalSuccess += $result['success'] ?? 0;
            $totalFailure += $result['failure'] ?? 0;
        }

        // 3. Nullify local references
        Student::whereIn('skyroom_user_id', $graduatedIds)
            ->update(['skyroom_user_id' => null]);

        return [
            'success' => $totalSuccess,
            'failure' => $totalFailure,
        ];
    }
}
```
