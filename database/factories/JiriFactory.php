<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jiri>
 */
class JiriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Jiri of '.fake()->word();
        $startsAt = Carbon::today()->addDays(rand(0, 60))->addHours(rand(6, 15));
        $endsAt = $startsAt->copy()->addHours(rand(1, 8));

        return [
            'name' => $name,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'slug' => Str::slug($name).'-'.$startsAt->format('d-m-y'),
            'session' => fake()->randomElement(['january', 'june', 'september']),
        ];
    }
}
