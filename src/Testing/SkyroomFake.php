<?php

namespace Skyroom\Laravel\Testing;

use PHPUnit\Framework\Assert as PHPUnit;
use Skyroom\Laravel\Contracts\SkyroomClientContract;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Resources\RoomsResource;
use Skyroom\Laravel\Resources\ServicesResource;
use Skyroom\Laravel\Resources\UsersResource;

class SkyroomFake implements SkyroomClientContract
{
    /**
     * @var array<int, array{action: string, params: array}>
     */
    protected array $recorded = [];

    protected ?ServicesResource $services = null;

    protected ?RoomsResource $rooms = null;

    protected ?UsersResource $users = null;

    /**
     * @param  array<string, mixed|callable>  $responses
     */
    public function __construct(
        protected array $responses = [],
    ) {}

    public function call(string $action, array $params = []): mixed
    {
        $this->recorded[] = [
            'action' => $action,
            'params' => $params,
        ];

        if (array_key_exists($action, $this->responses)) {
            $response = $this->responses[$action];

            if (is_callable($response)) {
                return $response($params);
            }

            return $response;
        }

        return match ($action) {
            'countRooms', 'countUsers', 'deleteRoom', 'deleteUser', 'updateRoom', 'updateUser', 'updateRoomUser' => 1,
            'createRoom', 'createUser', 'addRoomUsers', 'removeRoomUsers', 'addUserRooms', 'removeUserRooms' => 1,
            'getRooms', 'getUsers', 'getServices', 'getRoomUsers', 'getUserRooms' => [],
            'getRoom', 'getUser' => [],
            'getRoomUrl', 'createLoginUrl' => 'https://www.skyroom.online/ch/room/fake-token',
            'deleteUsers' => ['success' => 1, 'failure' => 0],
            default => null,
        };
    }

    public function services(): ServicesResource
    {
        return $this->services ??= new ServicesResource($this);
    }

    public function rooms(): RoomsResource
    {
        return $this->rooms ??= new RoomsResource($this);
    }

    public function users(): UsersResource
    {
        return $this->users ??= new UsersResource($this);
    }

    public function createLoginUrl(array $params): string
    {
        return (string) $this->call('createLoginUrl', $params);
    }

    // Convenience forwarding methods
    public function getServices(): array
    {
        return $this->services()->all();
    }

    public function getRooms(): array
    {
        return $this->rooms()->all();
    }

    public function countRooms(): int
    {
        return $this->rooms()->count();
    }

    public function getRoom(int|string|array $idOrName): array
    {
        return $this->rooms()->get($idOrName);
    }

    public function getRoomUrl(int $roomId, string $language = 'fa'): string
    {
        return $this->rooms()->url($roomId, $language);
    }

    public function createRoom(array $params): int
    {
        return $this->rooms()->create($params);
    }

    public function updateRoom(int $roomId, array $params): int
    {
        return $this->rooms()->update($roomId, $params);
    }

    public function deleteRoom(int $roomId): int
    {
        return $this->rooms()->delete($roomId);
    }

    public function getRoomUsers(int $roomId): array
    {
        return $this->rooms()->users($roomId);
    }

    public function addRoomUsers(int $roomId, array $users): int
    {
        return $this->rooms()->addUsers($roomId, $users);
    }

    public function removeRoomUsers(int $roomId, array $users): int
    {
        return $this->rooms()->removeUsers($roomId, $users);
    }

    public function updateRoomUser(int $roomId, int $userId, AccessLevel|int $access): int
    {
        return $this->rooms()->updateUser($roomId, $userId, $access);
    }

    public function getUsers(): array
    {
        return $this->users()->all();
    }

    public function countUsers(): int
    {
        return $this->users()->count();
    }

    public function getUser(int|string|array $idOrUsername): array
    {
        return $this->users()->get($idOrUsername);
    }

    public function createUser(array $params): int
    {
        return $this->users()->create($params);
    }

    public function updateUser(int $userId, array $params): int
    {
        return $this->users()->update($userId, $params);
    }

    public function deleteUser(int $userId): int
    {
        return $this->users()->delete($userId);
    }

    public function deleteUsers(array $users): array
    {
        return $this->users()->deleteMany($users);
    }

    public function getUserRooms(int $userId): array
    {
        return $this->users()->rooms($userId);
    }

    public function addUserRooms(int $userId, array $rooms): int
    {
        return $this->users()->addRooms($userId, $rooms);
    }

    public function removeUserRooms(int $userId, array $rooms): int
    {
        return $this->users()->removeRooms($userId, $rooms);
    }

    /**
     * Assert an action was sent.
     */
    public function assertSent(string $action, ?callable $callback = null): void
    {
        $matching = array_filter($this->recorded, fn ($call) => $call['action'] === $action);

        PHPUnit::assertTrue(
            count($matching) > 0,
            "The expected [{$action}] action was not sent."
        );

        if ($callback !== null) {
            $passed = false;
            foreach ($matching as $call) {
                if ($callback($call['params'])) {
                    $passed = true;
                    break;
                }
            }

            PHPUnit::assertTrue($passed, "The action [{$action}] was sent, but the callback conditions failed.");
        }
    }

    /**
     * Assert an action was not sent.
     */
    public function assertNotSent(string $action, ?callable $callback = null): void
    {
        $matching = array_filter($this->recorded, fn ($call) => $call['action'] === $action);

        if ($callback === null) {
            PHPUnit::assertCount(
                0,
                $matching,
                "Unexpected [{$action}] action was sent."
            );

            return;
        }

        foreach ($matching as $call) {
            PHPUnit::assertFalse(
                $callback($call['params']),
                "An unexpected [{$action}] action matching the callback was sent."
            );
        }
    }

    /**
     * Assert nothing was sent.
     */
    public function assertNothingSent(): void
    {
        PHPUnit::assertEmpty($this->recorded, 'Expected no actions to be sent, but some were sent.');
    }

    /**
     * Assert count of actions sent.
     */
    public function assertSentCount(int $count, ?string $action = null): void
    {
        if ($action !== null) {
            $matching = array_filter($this->recorded, fn ($call) => $call['action'] === $action);
            PHPUnit::assertCount($count, $matching);

            return;
        }

        PHPUnit::assertCount($count, $this->recorded);
    }

    /**
     * Get recorded calls.
     */
    public function recorded(?string $action = null): array
    {
        if ($action !== null) {
            return array_values(array_filter($this->recorded, fn ($call) => $call['action'] === $action));
        }

        return $this->recorded;
    }
}
