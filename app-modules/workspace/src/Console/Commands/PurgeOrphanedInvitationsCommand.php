<?php

namespace Modules\Workspace\Console\Commands;

use Illuminate\Console\Command;
use Modules\Workspace\Models\WorkspaceInvitation;

class PurgeOrphanedInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workspace:purge-orphaned-invitations {--age=60 : Age in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge orphaned invitations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $ageInDays = max(0, $this->option('age'));

        $this->info('Purging all open invitations older than '.$ageInDays.' days...');

        $deletedInvitations = WorkspaceInvitation::query()
            ->where('created_at', '<', now()->subDays($ageInDays))
            ->delete();

        $this->info($deletedInvitations.' invitations were deleted.');

        return self::SUCCESS;
    }
}
