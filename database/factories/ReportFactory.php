<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Report> */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'reporter_id' => User::factory(), 'code' => 'AK-'.now()->year.'-'.fake()->unique()->numerify('######'),
            'lat' => fake()->randomFloat(6, -3.6, -3.0), 'lng' => fake()->randomFloat(6, 105.0, 105.8),
            'land_type' => fake()->randomElement(['gambut', 'hutan', 'semak', 'kebun', 'lainnya']),
            'signs' => ['asap'], 'status' => 'baru', 'priority_score' => 0, 'priority_level' => 'rendah',
        ];
    }
}
