<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\Enums\AccessLevel;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class RoomsApiTest extends TestCase
{
    protected SkyroomClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new SkyroomClient(
            apiKey: 'test-api-key',
            baseUrl: 'https://www.skyroom.online/skyroom/api/'
        );
    }

    public function test_get_rooms(): void
    {
        $rooms = [
            ['id' => 1, 'service_id' => 8230, 'name' => 'meeting', 'title' => 'اتاق جلسات', 'status' => 1],
            ['id' => 2, 'service_id' => 8230, 'name' => 'learning-php', 'title' => 'PHP کلاس آموزش', 'status' => 0],
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $rooms,
            ]),
        ]);

        $this->assertSame($rooms, $this->client->rooms()->all());
        $this->assertSame($rooms, $this->client->getRooms());
    }

    public function test_count_rooms(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 5,
            ]),
        ]);

        $this->assertSame(5, $this->client->rooms()->count());
        $this->assertSame(5, $this->client->countRooms());
    }

    public function test_get_room_by_id(): void
    {
        $room = [
            'id' => 1,
            'service_id' => 8230,
            'name' => 'meeting',
            'title' => 'اتاق جلسات',
            'description' => null,
            'status' => 1,
            'guest_login' => false,
            'guest_limit' => 0,
            'op_login_first' => true,
            'max_users' => 8,
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $room,
            ]),
        ]);

        $result = $this->client->rooms()->get(1);
        $this->assertSame($room, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getRoom' && $req['params'] === ['room_id' => 1]);
    }

    public function test_get_room_by_name(): void
    {
        $room = ['id' => 1, 'name' => 'meeting'];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $room,
            ]),
        ]);

        $result = $this->client->rooms()->get('meeting');
        $this->assertSame($room, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getRoom' && $req['params'] === ['name' => 'meeting']);
    }

    public function test_get_room_url(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 'https://www.skyroom.online/ch/paramooz/meeting',
            ]),
        ]);

        $url = $this->client->rooms()->url(1, 'en');
        $this->assertSame('https://www.skyroom.online/ch/paramooz/meeting', $url);

        Http::assertSent(fn ($req) => $req['action'] === 'getRoomUrl' && $req['params'] === ['room_id' => 1, 'language' => 'en']);
    }

    public function test_create_room(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 456,
            ]),
        ]);

        $roomId = $this->client->rooms()->create([
            'name' => 'math-exercise',
            'title' => 'کلاس حل تمرین ریاضی',
            'guest_login' => false,
            'op_login_first' => true,
            'max_users' => 10,
        ]);

        $this->assertSame(456, $roomId);

        Http::assertSent(fn ($req) => $req['action'] === 'createRoom' && $req['params']['name'] === 'math-exercise');
    }

    public function test_update_room(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 1,
            ]),
        ]);

        $result = $this->client->rooms()->update(3, ['max_users' => 20]);
        $this->assertSame(1, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'updateRoom' && $req['params'] === ['room_id' => 3, 'max_users' => 20]);
    }

    public function test_delete_room(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 1,
            ]),
        ]);

        $result = $this->client->rooms()->delete(3);
        $this->assertSame(1, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'deleteRoom' && $req['params'] === ['room_id' => 3]);
    }

    public function test_get_room_users(): void
    {
        $users = [
            ['user_id' => 6344, 'username' => 'operator', 'nickname' => 'اپراتور', 'access' => 3],
            ['user_id' => 6345, 'username' => 'presenter', 'nickname' => 'ارایه کننده', 'access' => 2],
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $users,
            ]),
        ]);

        $result = $this->client->rooms()->users(2);
        $this->assertSame($users, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getRoomUsers' && $req['params'] === ['room_id' => 2]);
    }

    public function test_add_room_users(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 2,
            ]),
        ]);

        $added = $this->client->rooms()->addUsers(1, [
            ['user_id' => 6344],
            ['user_id' => 6345, 'access' => 2],
        ]);

        $this->assertSame(2, $added);

        Http::assertSent(fn ($req) => $req['action'] === 'addRoomUsers' && count($req['params']['users']) === 2);
    }

    public function test_remove_room_users(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 2,
            ]),
        ]);

        $removed = $this->client->rooms()->removeUsers(1, [6344, 6345]);
        $this->assertSame(2, $removed);

        Http::assertSent(fn ($req) => $req['action'] === 'removeRoomUsers' && $req['params']['users'] === [6344, 6345]);
    }

    public function test_update_room_user_with_enum(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 1,
            ]),
        ]);

        $result = $this->client->rooms()->updateUser(1, 6344, AccessLevel::OPERATOR);
        $this->assertSame(1, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'updateRoomUser' && $req['params'] === [
            'room_id' => 1,
            'user_id' => 6344,
            'access' => 3,
        ]);
    }
}
