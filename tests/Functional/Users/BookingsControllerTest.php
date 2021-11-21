<?php

namespace Tests\Functional\Bookings;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\FunctionalTestCase;

class BookingsControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/bookings';

    public function setUp(): void
    {
        parent::setUp();

        // testing the API key auth or others things like that is not the job of this test
        $this->withoutMiddleware();
    }

    public function testListingBookings()
    {
        DB::table('bookings')->truncate();

        $trip1 = Booking::factory()->create();
        $trip2 = Booking::factory()->create();
        $trip3 = Booking::factory()->create();

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(2, count($response['data']));
        $this->assertSame($trip1->id, $response['data'][0]['id']);
        $this->assertSame($trip2->id, $response['data'][1]['id']);

        $this->assertSame(1, $response['meta']['current_page']);
        $this->assertSame('2', $response['meta']['per_page']);
        $this->assertSame(3, $response['meta']['total']);

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2, 'page' => 2]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(1, count($response['data']));
        $this->assertSame($trip3->id, $response['data'][0]['id']);

        $this->json('GET', $this->createUri(self::ENDPOINT, ['perPage' => 2, 'page' => 26]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(0, count($response['data']));
    }

    public function testUsersCanCreateBookings()
    {
        $this->markTestSkipped('Problems with the JWT package during tests');

        $user = User::factory()->create();
        $trip = Trip::factory()->create();

        $this->actingAs($user)
             ->json('POST', self::ENDPOINT, [
                'tripSlug' => $trip->slug,
             ]);

        $this->assertResponseOk();
    }
}
