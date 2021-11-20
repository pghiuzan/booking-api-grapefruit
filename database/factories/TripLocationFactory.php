<?php

namespace Database\Factories;

use App\Models\TripLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripLocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TripLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'city' => $this->faker->city,
            'country' => $this->faker->country,
        ];
    }
}
