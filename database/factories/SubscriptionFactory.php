<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'member_id' => Member::factory(),
            'plan_type' => fake()->randomElement(['monthly', 'quarterly', 'annual']),
            'amount' => fake()->randomElement([10000, 25000, 80000]),
            'payment_method' => fake()->randomElement(['mobile_money', 'bank_transfer', 'cash']),
            'payment_reference' => fake()->unique()->numerify('PAY-########'),
            'start_date' => $startDate,
            'end_date' => now()->addMonth(),
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => 'expired',
            'start_date' => now()->subMonths(3),
            'end_date' => now()->subMonth(),
        ]);
    }
}
