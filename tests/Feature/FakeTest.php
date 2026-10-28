<?php

namespace Skyroom\Laravel\Tests\Feature;

use Skyroom\Laravel\Facades\Skyroom;
use Skyroom\Laravel\Tests\TestCase;

class FakeTest extends TestCase
{
    public function test_skyroom_fake_records_and_asserts_calls(): void
    {
        Skyroom::fake([
            'getRooms' => [
                ['id' => 10, 'name' => 'faked-room'],
            ],
            'createRoom' => 777,
        ]);

        Skyroom::assertNothingSent();

        $rooms = Skyroom::rooms()->all();
        $this->assertSame([['id' => 10, 'name' => 'faked-room']], $rooms);

        $roomId = Skyroom::createRoom(['name' => 'test-room', 'title' => 'Test Room']);
        $this->assertSame(777, $roomId);

        Skyroom::assertSent('getRooms');
        Skyroom::assertSent('createRoom', fn ($params) => $params['name'] === 'test-room');
        Skyroom::assertNotSent('deleteRoom');
        Skyroom::assertSentCount(2);
        Skyroom::assertSentCount(1, 'createRoom');
    }

    public function test_skyroom_fake_with_closure_response(): void
    {
        Skyroom::fake([
            'createRoom' => function (array $params) {
                return $params['name'] === 'vip' ? 999 : 111;
            },
        ]);

        $this->assertSame(999, Skyroom::createRoom(['name' => 'vip']));
        $this->assertSame(111, Skyroom::createRoom(['name' => 'standard']));

        Skyroom::assertSentCount(2, 'createRoom');
    }

    public function test_skyroom_fake_default_responses(): void
    {
        Skyroom::fake();

        $this->assertSame(1, Skyroom::countRooms());
        $this->assertSame(1, Skyroom::countUsers());
        $this->assertSame([], Skyroom::getServices());
        $this->assertStringContainsString('skyroom.online', Skyroom::createLoginUrl(['room_id' => 1]));
    }
}
