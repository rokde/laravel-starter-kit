<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'role' => 'member',
        ];
    }
}
