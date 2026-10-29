<?php

namespace Database\Factories;

use App\Models\Hotspot;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Hotspot> */
class HotspotFactory extends Factory
{
    protected $model = Hotspot::class;

    public function definition(): array
    {
        return ['lat' => fake()->randomFloat(6, -3.6, -3.0), 'lng' => fake()->randomFloat(6, 105.0, 105.8), 'confidence' => fake()->numberBetween(30, 100), 'detected_at' => fake()->dateTimeBetween('-12 months'), 'source' => 'SIMULASI', 'is_simulated' => true];
    }
}
