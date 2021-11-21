<?php

namespace Tests\Functional\Trips;

use App\Models\Trip;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\FunctionalTestCase;

class TripsControllerTest extends FunctionalTestCase
{
    private const ENDPOINT = '/trips';

    public function setUp(): void
    {
        parent::setUp();

        // testing the API key auth or others things like that is not the job of this test
        $this->withoutMiddleware();
    }

    public function testListingTrips()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('trips')->truncate();
        Schema::enableForeignKeyConstraints();

        $trip1 = Trip::factory()->create();
        $trip2 = Trip::factory()->create();
        $trip3 = Trip::factory()->create();

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

    public function testGettingOneTrip()
    {
        $trip = Trip::factory()->create();

        $this->json('GET', sprintf('%s/%s', self::ENDPOINT, $trip->slug));
        $this->assertResponseOk();
        $this->seeJson([
            'id' => $trip->id,
            'slug' => $trip->slug,
            'title' => $trip->title,
        ]);
    }

    public function testGettingOneTripThrowsNotFoundExceptionWhenMissinTrip()
    {
        $this->json('GET', sprintf('%s/%s', self::ENDPOINT, 'no-trip-with-this-slug'));
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testCreateTrip()
    {
        $this->json('POST', self::ENDPOINT, [
            'title' => 'Super Awesome Trip',
            'description' => 'Really Awesome Trip',
            'start_date' => '2021-01-01 10:00:00',
            'end_date' => '2021-01-01 19:00:00',
            'location' => [
                'city' => 'Casablanca',
                'country' => 'Morocco',
            ],
            'price' => '199.99',
        ]);
        $this->assertResponseStatus(Response::HTTP_CREATED);
        $this->seeJson([
            'slug' => 'super-awesome-trip'
        ]);
        $this->seeInDatabase('trips', [
            'slug' => 'super-awesome-trip',
        ]);
    }

    public function testCreateTripWithExistingTitle()
    {
        Trip::factory()->create([
            'slug' => 'super-awesome-trip',
        ]);

        $this->json('POST', self::ENDPOINT, [
            'title' => 'Super Awesome Trip',
            'description' => 'Really Awesome Trip',
            'start_date' => '2021-01-01 10:00:00',
            'end_date' => '2021-01-01 19:00:00',
            'location' => [
                'city' => 'Casablanca',
                'country' => 'Morocco',
            ],
            'price' => '199.99',
        ]);

        $this->seeJson([
            'slug' => 'super-awesome-trip-2'
        ]);
        $this->seeInDatabase('trips', [
            'slug' => 'super-awesome-trip',
        ]);
        $this->seeInDatabase('trips', [
            'slug' => 'super-awesome-trip-2',
        ]);
    }

    public function testUpdateTrip()
    {
        Trip::factory()->create([
            'slug' => 'super-awesome-trip',
        ]);

        $this->json('PATCH', sprintf('%s/super-awesome-trip', self::ENDPOINT), [
            'title' => 'Super Awesome Trip',
            'description' => 'Really Awesome Trip',
            'start_date' => '2021-01-01 10:00:00',
            'end_date' => '2021-01-01 19:00:00',
            'location' => [
                'city' => 'Casablanca',
                'country' => 'Morocco',
            ],
            'price' => '199.99',
        ]);

        $this->seeJson([
            'slug' => 'super-awesome-trip'
        ]);
        $this->seeInDatabase('trips', [
            'slug' => 'super-awesome-trip',
            'title' => 'Super Awesome Trip',
            'description' => 'Really Awesome Trip',
            'start_date' => '2021-01-01 10:00:00',
            'end_date' => '2021-01-01 19:00:00',
            'price' => '199.99',
        ]);
    }

    public function testDeleteTrip()
    {
        $trip = Trip::factory()->create();

        $this->json('DELETE', sprintf('%s/%d', self::ENDPOINT, $trip->id));
        $this->assertResponseOk();
        $this->seeInDatabase('trips', [
            'id' => $trip->id,
        ]);
        $this->notSeeInDatabase('trips', [
            'id' => $trip->id,
            'deleted_at' => null
        ]);
    }

    public function testSearchTrips()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('trips')->truncate();
        Schema::enableForeignKeyConstraints();

        $trip1 = Trip::factory()->create([
            'title' => 'Test trip 1',
            'price' => 100,
        ]);
        $trip2 = Trip::factory()->create([
            'title' => 'trip 2',
            'description' => 'Second test trip',
            'price' => 200,
        ]);
        Trip::factory()->create([
            'title' => 'Super Awesome Trip',
            'description' => 'This is the best trip ever',
        ]);

        $this->json('GET', $this->createUri(self::ENDPOINT . '/search', [
            'search' => 'test',
            'orderBy' => 'price',
            'orderDirection' => 'desc',
            'minPrice' => 0,
            'maxPrice' => 999999999.99,
        ]));
        $this->assertResponseOk();

        $response = json_decode($this->response->getContent(), true);
        $this->assertSame(2, count($response['data']));
        $this->assertSame($trip2->id, $response['data'][0]['id']);
        $this->assertSame($trip1->id, $response['data'][1]['id']);
    }
}
