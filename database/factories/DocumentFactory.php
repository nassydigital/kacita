<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'documentable_id' => Member::factory(),
            'documentable_type' => Member::class,
            'filename' => fake()->word().'.pdf',
            'path' => 'documents/'.fake()->uuid().'.pdf',
            'type' => fake()->randomElement(['id_copy', 'proof_of_residence', 'photo', 'certificate']),
        ];
    }

    public function forMember(?Member $member = null): static
    {
        return $this->state(fn () => [
            'documentable_id' => $member?->id ?? Member::factory(),
            'documentable_type' => Member::class,
        ]);
    }
}
