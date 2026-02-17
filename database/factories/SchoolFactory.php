<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' '.fake()->randomElement(['Primary School', 'Secondary School', 'Academy']),
            'category' => fake()->randomElement(['primary', 'secondary', 'tertiary']),
            'address' => fake()->address(),
            'contact_name' => fake()->name(),
            'contact_phone' => fake()->e164PhoneNumber(),
            'contact_email' => fake()->safeEmail(),
        ];
    }
}
