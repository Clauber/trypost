<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CommentAutomation\RunStatus;
use App\Models\CommentAutomation;
use App\Models\CommentAutomationRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommentAutomationRun>
 */
class CommentAutomationRunFactory extends Factory
{
    protected $model = CommentAutomationRun::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment_automation_id' => CommentAutomation::factory(),
            'comment_id' => (string) $this->faker->randomNumber(9),
            'commenter_username' => $this->faker->userName(),
            'status' => RunStatus::Sent,
            'error' => null,
        ];
    }
}
