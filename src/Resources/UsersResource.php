<?php

namespace Skyroom\Laravel\Resources;

use Skyroom\Laravel\Contracts\SkyroomClientContract;

class UsersResource
{
    public function __construct(
        protected SkyroomClientContract $client,
    ) {}

    /**
     * Get list of users.
     */
    public function all(): array
    {
        return (array) $this->client->call('getUsers');
    }

    /**
     * Get total count of users.
     */
    public function count(): int
    {
        return (int) $this->client->call('countUsers');
    }

    /**
     * Get user details by ID, username, or params array.
     */
    public function get(int|string|array $idOrUsername): array
    {
        $params = match (true) {
            is_array($idOrUsername) => $idOrUsername,
            is_int($idOrUsername) => ['user_id' => $idOrUsername],
            is_string($idOrUsername) => ['username' => $idOrUsername],
        };

        return (array) $this->client->call('getUser', $params);
    }

    /**
     * Create a new user.
     */
    public function create(array $params): int
    {
        return (int) $this->client->call('createUser', $params);
    }

    /**
     * Update user details.
     */
    public function update(int $userId, array $params): int
    {
        return (int) $this->client->call('updateUser', array_merge(['user_id' => $userId], $params));
    }

    /**
     * Delete a single user.
     */
    public function delete(int $userId): int
    {
        return (int) $this->client->call('deleteUser', ['user_id' => $userId]);
    }

    /**
     * Delete multiple users.
     */
    public function deleteMany(array $users): array
    {
        return (array) $this->client->call('deleteUsers', ['users' => $users]);
    }

    /**
     * Get list of rooms assigned to a user.
     */
    public function rooms(int $userId): array
    {
        return (array) $this->client->call('getUserRooms', ['user_id' => $userId]);
    }

    /**
     * Add rooms access to a user.
     */
    public function addRooms(int $userId, array $rooms): int
    {
        return (int) $this->client->call('addUserRooms', [
            'user_id' => $userId,
            'rooms' => $rooms,
        ]);
    }

    /**
     * Remove rooms access from a user.
     */
    public function removeRooms(int $userId, array $rooms): int
    {
        return (int) $this->client->call('removeUserRooms', [
            'user_id' => $userId,
            'rooms' => $rooms,
        ]);
    }
}
