<?php

namespace Skyroom\Laravel\Resources;

use Skyroom\Laravel\Contracts\SkyroomClientContract;
use Skyroom\Laravel\Enums\AccessLevel;

class RoomsResource
{
    public function __construct(
        protected SkyroomClientContract $client,
    ) {
    }

    /**
     * Get list of rooms.
     */
    public function all(): array
    {
        return (array) $this->client->call('getRooms');
    }

    /**
     * Get total count of rooms.
     */
    public function count(): int
    {
        return (int) $this->client->call('countRooms');
    }

    /**
     * Get room details by ID, name, or params array.
     */
    public function get(int|string|array $idOrName): array
    {
        $params = match (true) {
            is_array($idOrName) => $idOrName,
            is_int($idOrName) => ['room_id' => $idOrName],
            is_string($idOrName) => ['name' => $idOrName],
        };

        return (array) $this->client->call('getRoom', $params);
    }

    /**
     * Get room URL.
     */
    public function url(int $roomId, string $language = 'fa'): string
    {
        return (string) $this->client->call('getRoomUrl', [
            'room_id' => $roomId,
            'language' => $language,
        ]);
    }

    /**
     * Create a new room.
     */
    public function create(array $params): int
    {
        return (int) $this->client->call('createRoom', $params);
    }

    /**
     * Update room details.
     */
    public function update(int $roomId, array $params): int
    {
        return (int) $this->client->call('updateRoom', array_merge(['room_id' => $roomId], $params));
    }

    /**
     * Delete a room.
     */
    public function delete(int $roomId): int
    {
        return (int) $this->client->call('deleteRoom', ['room_id' => $roomId]);
    }

    /**
     * Get list of users with access to a room.
     */
    public function users(int $roomId): array
    {
        return (array) $this->client->call('getRoomUsers', ['room_id' => $roomId]);
    }

    /**
     * Add users access to a room.
     */
    public function addUsers(int $roomId, array $users): int
    {
        return (int) $this->client->call('addRoomUsers', [
            'room_id' => $roomId,
            'users' => $users,
        ]);
    }

    /**
     * Remove users access from a room.
     */
    public function removeUsers(int $roomId, array $users): int
    {
        return (int) $this->client->call('removeRoomUsers', [
            'room_id' => $roomId,
            'users' => $users,
        ]);
    }

    /**
     * Update user access level for a room.
     */
    public function updateUser(int $roomId, int $userId, AccessLevel|int $access): int
    {
        $accessValue = $access instanceof AccessLevel ? $access->value : $access;

        return (int) $this->client->call('updateRoomUser', [
            'room_id' => $roomId,
            'user_id' => $userId,
            'access' => $accessValue,
        ]);
    }
}
