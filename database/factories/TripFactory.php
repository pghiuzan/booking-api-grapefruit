<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\TripLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class TripFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Trip::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $location = TripLocation::factory()->create();

        $title = $this->faker->colorName . ' ' . $this->faker->randomNumber(5);
        return [
            'slug' => Str::slug($title),
            'title' => $title,
            'description' => $this->faker->text,
            'start_date' => Carbon::now()->subDays(7),
            'end_date' => Carbon::now(),
            'location_id' => $location->id,
            'price' => $this->faker->randomFloat(),
        ];
    }
}
