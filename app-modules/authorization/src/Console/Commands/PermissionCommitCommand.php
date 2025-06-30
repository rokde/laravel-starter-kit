<?php

declare(strict_types=1);

namespace Modules\Authorization\Console\Commands;

use Illuminate\Console\Command;
use Modules\Authorization\Permissions\PermissionRegistry;

class PermissionCommitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:commit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This persists the permissions to the database.';

    public function handle(): int
    {
        app(PermissionRegistry::class)
            ->commit();

        return self::SUCCESS;
    }
}
