<?php

namespace Database\Factories;

use App\Models\PointOfInterest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PointOfInterest> */
class PointOfInterestFactory extends Factory
{
    protected $model = PointOfInterest::class;

    public function definition(): array
    {
        return ['name' => fake()->streetName(), 'kind' => fake()->randomElement(['permukiman', 'sekolah', 'faskes']), 'lat' => fake()->randomFloat(6, -3.6, -3.0), 'lng' => fake()->randomFloat(6, 105.0, 105.8), 'is_simulated' => true];
    }
}
