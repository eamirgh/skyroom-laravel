<?php

namespace Skyroom\Laravel\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Skyroom\Laravel\SkyroomClient;
use Skyroom\Laravel\Tests\TestCase;

class UsersApiTest extends TestCase
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

    public function test_get_users(): void
    {
        $users = [
            ['id' => 1, 'username' => 'parham', 'nickname' => 'پرهام', 'status' => 1],
            ['id' => 2, 'username' => 'sara', 'nickname' => 'سارا', 'status' => 1],
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $users,
            ]),
        ]);

        $this->assertSame($users, $this->client->users()->all());
        $this->assertSame($users, $this->client->getUsers());
    }

    public function test_count_users(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 150,
            ]),
        ]);

        $this->assertSame(150, $this->client->users()->count());
        $this->assertSame(150, $this->client->countUsers());
    }

    public function test_get_user_by_id(): void
    {
        $user = [
            'id' => 1,
            'username' => 'parham',
            'nickname' => 'پرهام',
            'email' => 'example@gmail.com',
            'fname' => 'پرهام',
            'lname' => 'عابدینی',
            'gender' => 1,
            'status' => 1,
            'is_public' => false,
            'concurrent' => 0,
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $user,
            ]),
        ]);

        $result = $this->client->users()->get(1);
        $this->assertSame($user, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getUser' && $req['params'] === ['user_id' => 1]);
    }

    public function test_get_user_by_username(): void
    {
        $user = ['id' => 1, 'username' => 'parham'];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $user,
            ]),
        ]);

        $result = $this->client->users()->get('parham');
        $this->assertSame($user, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getUser' && $req['params'] === ['username' => 'parham']);
    }

    public function test_create_user(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 3,
            ]),
        ]);

        $userId = $this->client->users()->create([
            'username' => 'test-user',
            'password' => '12345',
            'nickname' => 'کاربر عمومی',
            'status' => 1,
            'is_public' => true,
        ]);

        $this->assertSame(3, $userId);

        Http::assertSent(fn ($req) => $req['action'] === 'createUser' && $req['params']['username'] === 'test-user');
    }

    public function test_update_user(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 1,
            ]),
        ]);

        $result = $this->client->users()->update(3, ['password' => 'tu2017!']);
        $this->assertSame(1, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'updateUser' && $req['params'] === ['user_id' => 3, 'password' => 'tu2017!']);
    }

    public function test_delete_user(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 1,
            ]),
        ]);

        $result = $this->client->users()->delete(3);
        $this->assertSame(1, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'deleteUser' && $req['params'] === ['user_id' => 3]);
    }

    public function test_delete_multiple_users(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => ['success' => 2, 'failure' => 1],
            ]),
        ]);

        $result = $this->client->users()->deleteMany([3, 4, 5]);
        $this->assertSame(['success' => 2, 'failure' => 1], $result);

        Http::assertSent(fn ($req) => $req['action'] === 'deleteUsers' && $req['params'] === ['users' => [3, 4, 5]]);
    }

    public function test_get_user_rooms(): void
    {
        $rooms = [
            ['room_id' => 1, 'name' => 'meeting', 'title' => 'اتاق جلسات', 'access' => 1],
            ['room_id' => 2, 'name' => 'learning-php', 'title' => 'PHP کلاس آموزش', 'access' => 2],
        ];

        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => $rooms,
            ]),
        ]);

        $result = $this->client->users()->rooms(1);
        $this->assertSame($rooms, $result);

        Http::assertSent(fn ($req) => $req['action'] === 'getUserRooms' && $req['params'] === ['user_id' => 1]);
    }

    public function test_add_user_rooms(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 2,
            ]),
        ]);

        $added = $this->client->users()->addRooms(2, [
            ['room_id' => 1],
            ['room_id' => 2, 'access' => 2],
        ]);

        $this->assertSame(2, $added);

        Http::assertSent(fn ($req) => $req['action'] === 'addUserRooms' && $req['params']['user_id'] === 2);
    }

    public function test_remove_user_rooms(): void
    {
        Http::fake([
            'https://www.skyroom.online/skyroom/api/test-api-key' => Http::response([
                'ok' => true,
                'result' => 2,
            ]),
        ]);

        $removed = $this->client->users()->removeRooms(2, [1, 2]);
        $this->assertSame(2, $removed);

        Http::assertSent(fn ($req) => $req['action'] === 'removeUserRooms' && $req['params'] === ['user_id' => 2, 'rooms' => [1, 2]]);
    }
}
