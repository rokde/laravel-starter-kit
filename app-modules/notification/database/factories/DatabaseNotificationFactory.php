<?php

declare(strict_types=1);

namespace Modules\Notification\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Illuminate\Notifications\DatabaseNotification>
 */
class DatabaseNotificationFactory extends Factory
{
    protected $model = DatabaseNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'type' => \Modules\Workspace\Notifications\MemberAcceptedNotification::class,
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => 1,
            'data' => ['test' => true],
            'read_at' => null,
        ];
    }
}
