<?php

namespace Skyroom\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Testing\SkyroomFake;

/**
 * @method static mixed call(string $action, array $params = [])
 * @method static \Skyroom\Laravel\Resources\ServicesResource services()
 * @method static \Skyroom\Laravel\Resources\RoomsResource rooms()
 * @method static \Skyroom\Laravel\Resources\UsersResource users()
 * @method static string createLoginUrl(array $params)
 * @method static array getServices()
 * @method static array getRooms()
 * @method static int countRooms()
 * @method static array getRoom(int|string|array $idOrName)
 * @method static string getRoomUrl(int $roomId, string $language = 'fa')
 * @method static int createRoom(array $params)
 * @method static int updateRoom(int $roomId, array $params)
 * @method static int deleteRoom(int $roomId)
 * @method static array getRoomUsers(int $roomId)
 * @method static int addRoomUsers(int $roomId, array $users)
 * @method static int removeRoomUsers(int $roomId, array $users)
 * @method static int updateRoomUser(int $roomId, int $userId, int $access)
 * @method static array getUsers()
 * @method static int countUsers()
 * @method static array getUser(int|string|array $idOrUsername)
 * @method static int createUser(array $params)
 * @method static int updateUser(int $userId, array $params)
 * @method static int deleteUser(int $userId)
 * @method static array deleteUsers(array $users)
 * @method static array getUserRooms(int $userId)
 * @method static int addUserRooms(int $userId, array $rooms)
 * @method static int removeUserRooms(int $userId, array $rooms)
 *
 * @see \Skyroom\Laravel\SkyroomClient
 */
class Skyroom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SkyroomClient::class;
    }

    /**
     * Replace the bound instance with a fake.
     */
    public static function fake(array $responses = []): SkyroomFake
    {
        static::swap($fake = new SkyroomFake($responses));

        return $fake;
    }
}
