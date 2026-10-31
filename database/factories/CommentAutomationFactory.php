<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CommentAutomation;
use App\Models\SocialAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentAutomation>
 */
class CommentAutomationFactory extends Factory
{
    protected $model = CommentAutomation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'social_account_id' => SocialAccount::factory()->instagram(),
            'workspace_id' => fn (array $attributes): string => SocialAccount::query()->findOrFail($attributes['social_account_id'])->workspace_id,
            'keyword' => $this->faker->word(),
            'reply_message' => $this->faker->sentence(),
            'public_reply' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
