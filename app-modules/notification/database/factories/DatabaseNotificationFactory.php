<?php

declare(strict_types=1);

namespace Modules\Notification\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Workspace\Notifications\MemberAcceptedNotification;

/**
 * @extends Factory<DatabaseNotification>
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
            'type' => MemberAcceptedNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => 1,
            'data' => ['test' => true],
            'read_at' => null,
        ];
    }
}
