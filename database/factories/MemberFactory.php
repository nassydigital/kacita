<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\QrCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'member_number' => 'MEM-'.strtoupper(Str::random(8)),
            'region' => fake()->randomElement(['Central', 'Eastern', 'Western', 'Northern']),
            'market' => fake()->randomElement(['Owino', 'Nakasero', 'Kikuubo', 'Wandegeya']),
            'id_type' => fake()->randomElement(['national_id', 'passport', 'driving_permit']),
            'id_number' => fake()->unique()->numerify('CM############'),
            'photo' => null,
            'registration_source' => 'web',
            'qr_campaign_id' => null,
            'status' => 'pending',
            'joined_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => 'active',
            'joined_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
        ]);
    }

    public function fromQrCampaign(?QrCampaign $campaign = null): static
    {
        return $this->state(fn () => [
            'registration_source' => 'qr',
            'qr_campaign_id' => $campaign?->id ?? QrCampaign::factory(),
        ]);
    }
}
