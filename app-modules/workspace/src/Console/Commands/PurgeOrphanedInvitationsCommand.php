<?php

declare(strict_types=1);

namespace Modules\Workspace\Console\Commands;

use Illuminate\Console\Command;
use Modules\Workspace\Models\WorkspaceInvitation;
use Override;

class PurgeOrphanedInvitationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    #[Override]
    protected $signature = 'workspace:purge-orphaned-invitations {--age=60 : Age in days}';

    /**
     * The console command description.
     *
     * @var string
     */
    #[Override]
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
