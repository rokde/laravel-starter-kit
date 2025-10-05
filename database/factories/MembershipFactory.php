<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'role' => 'member',
        ];
    }
}
