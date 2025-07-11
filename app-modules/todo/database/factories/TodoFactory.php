<?php

declare(strict_types=1);

namespace Modules\Todo\Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Workspace\Models\Workspace;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Todo\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'completed' => $this->faker->boolean(20), // 20% chance of being completed
            'workspace_id' => Workspace::factory(),
            'user_id' => User::factory(),
            'due_date' => null,
        ];
    }

    /**
     * Indicate that the todo is completed.
     */
    public function completed(): self
    {
        return $this->state(fn (array $attributes): array => [
            'completed' => true,
        ]);
    }

    /**
     * Indicate that the todo is not completed.
     */
    public function incomplete(): self
    {
        return $this->state(fn (array $attributes): array => [
            'completed' => false,
        ]);
    }

    public function due(Carbon $value): self
    {
        return $this->state(fn (array $attributes): array => [
            'due_date' => $value,
        ]);
    }
}
