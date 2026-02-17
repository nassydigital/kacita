<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'channel' => fake()->randomElement(['sms', 'email', 'push']),
            'target_group' => fake()->randomElement(['all_members', 'active_members', 'pending_members']),
            'subject' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'sent_by' => User::factory(),
            'status' => 'draft',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => 'sent',
            'sent_at' => now(),
            'delivery_count' => fake()->numberBetween(10, 500),
        ]);
    }
}
