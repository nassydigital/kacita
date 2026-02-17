<?php

namespace Database\Factories;

use App\Models\BursaryApplication;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BursaryApplication>
 */
class BursaryApplicationFactory extends Factory
{
    protected $model = BursaryApplication::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'school_id' => School::factory(),
            'academic_year' => fake()->randomElement(['2025', '2026']),
            'amount_requested' => fake()->randomFloat(2, 100000, 2000000),
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'reference_number' => 'BUR-'.strtoupper(Str::random(8)),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'amount_approved' => fake()->randomFloat(2, 100000, 2000000),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
            'reviewed_at' => now(),
        ]);
    }
}
