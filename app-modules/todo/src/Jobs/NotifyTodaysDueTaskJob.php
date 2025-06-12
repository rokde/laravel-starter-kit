<?php

declare(strict_types=1);

namespace Modules\Todo\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Todo\Models\Todo;
use Modules\Todo\Notifications\TodoIsDueTodayNotification;

class NotifyTodaysDueTaskJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Todo::query()
            ->where('completed', false)
            ->whereToday('due_date')
            ->get()
            ->each(function (Todo $todo): void {
                $todo->user->notify(new TodoIsDueTodayNotification($todo));
            });
    }
}
