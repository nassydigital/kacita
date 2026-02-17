<?php

namespace Database\Factories;

use App\Models\QrCampaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<QrCampaign>
 */
class QrCampaignFactory extends Factory
{
    protected $model = QrCampaign::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true).' Campaign',
            'market_location' => fake()->randomElement(['Owino', 'Nakasero', 'Kikuubo', 'Wandegeya']),
            'code' => 'QR-'.strtoupper(Str::random(8)),
            'registrations_count' => 0,
            'created_by' => User::factory(),
        ];
    }
}
