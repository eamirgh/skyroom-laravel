<?php

namespace Skyroom\Laravel;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\Contracts\SkyroomClientContract;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\Exceptions\AuthenticationException;
use Skyroom\Laravel\Exceptions\NetworkException;
use Skyroom\Laravel\Exceptions\RateLimitException;
use Skyroom\Laravel\Exceptions\SkyroomApiException;
use Skyroom\Laravel\Resources\RoomsResource;
use Skyroom\Laravel\Resources\ServicesResource;
use Skyroom\Laravel\Resources\UsersResource;

class SkyroomClient implements SkyroomClientContract
{
    protected ?ServicesResource $services = null;
    protected ?RoomsResource $rooms = null;
    protected ?UsersResource $users = null;

    public function __construct(
        protected string $apiKey,
        protected string $baseUrl = 'https://www.skyroom.online/skyroom/api/',
        protected int $timeout = 30,
        protected array $retry = ['times' => 3, 'sleep_milliseconds' => 100],
    ) {
    }

    /**
     * Call any raw action on Skyroom API.
     *
     * @throws SkyroomApiException
     * @throws RateLimitException
     * @throws NetworkException
     * @throws AuthenticationException
     */
    public function call(string $action, array $params = []): mixed
    {
        if (trim($this->apiKey) === '') {
            throw new AuthenticationException('Skyroom API key is not configured.');
        }

        $url = rtrim($this->baseUrl, '/') . '/' . $this->apiKey;

        $payload = ['action' => $action];
        if (!empty($params)) {
            $payload['params'] = $params;
        }

        $request = Http::asJson()
            ->acceptJson()
            ->timeout($this->timeout);

        $times = (int) ($this->retry['times'] ?? 1);
        $sleep = (int) ($this->retry['sleep_milliseconds'] ?? 100);

        if ($times > 1) {
            $request->retry(
                $times,
                $sleep,
                function (\Throwable $exception) {
                    if ($exception instanceof ConnectionException) {
                        return true;
                    }

                    if ($exception instanceof \Illuminate\Http\Client\RequestException && $exception->response?->status() === 503) {
                        return true;
                    }

                    return false;
                },
                throw: false
            );
        }

        try {
            $response = $request->post($url, $payload);
        } catch (\Throwable $e) {
            throw new NetworkException('Failed to connect to Skyroom API: ' . $e->getMessage(), (int) $e->getCode(), $e);
        }

        if ($response->status() === 503) {
            throw new RateLimitException('Skyroom API rate limit exceeded or service unavailable (503).', 503);
        }

        if (!$response->successful()) {
            throw new NetworkException(
                'Skyroom API request failed with HTTP status ' . $response->status(),
                $response->status()
            );
        }

        $data = $response->json();

        if (!is_array($data) || !array_key_exists('ok', $data)) {
            throw new NetworkException('Invalid JSON response received from Skyroom API.');
        }

        if ($data['ok'] === true) {
            return $data['result'] ?? null;
        }

        $errorCode = (int) ($data['error_code'] ?? 0);
        $errorMessage = (string) ($data['error_message'] ?? 'Unknown Skyroom API error');

        throw SkyroomApiException::fromResponse($errorCode, $errorMessage, $action, $params);
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

    /**
     * Create direct login URL for a room.
     */
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
}
