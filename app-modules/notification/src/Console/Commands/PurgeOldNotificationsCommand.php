<?php

namespace Modules\Notification\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;

class PurgeOldNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:purge {--age=60 : Age in days} {--include-unread : Include unread notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old read notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ageInDays = max(0, $this->option('age'));
        $includeUnread = $this->option('include-unread');

        $this->info('Purging all read notifications older than ' . $ageInDays . ' days...');

        $deletedNotifications = DatabaseNotification::query()
            ->where('created_at', '<', now()->subDays($ageInDays))
            ->read()
            ->delete();

        if ($includeUnread) {
            $deletedNotifications += DatabaseNotification::query()
                ->where('created_at', '<', now()->subDays($ageInDays))
                ->delete();
        }

        $this->info($deletedNotifications . ' notifications were deleted.');

        return self::SUCCESS;
    }
}
