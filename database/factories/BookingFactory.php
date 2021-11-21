<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\TripLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $trip = Trip::factory()->create();
        $user = User::factory()->create();

        return [
            'trip_id' => $trip->id,
            'user_id' => $user->id,
            'status' => $this->faker->randomElement([
                Booking::STATUS_RESERVED,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_CANCELLED
            ]),
        ];
    }
}
