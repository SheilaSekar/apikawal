<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Verification> */
class VerificationFactory extends Factory
{
    protected $model = Verification::class;

    public function definition(): array
    {
        return ['report_id' => Report::factory(), 'user_id' => User::factory(), 'vote' => fake()->randomElement(['konfirmasi', 'tidak_terlihat', 'sudah_padam']), 'weight' => 1];
    }
}
