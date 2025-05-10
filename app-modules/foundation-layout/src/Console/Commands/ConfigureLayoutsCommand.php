<?php

namespace Modules\FoundationLayout\Console\Commands;

use Illuminate\Console\Command;
use Modules\FoundationLayout\Service\LayoutService;

class ConfigureLayoutsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:configure-layouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configures used layouts';

    /**
     * Execute the console command.
     */
    public function handle(LayoutService $layoutService): int
    {
        $this->info('Configure authentication layout.');

        $authenticationLayoutSelected = $this->choice(
            question: 'Which layout do you want to use for the authentication screen?',
            choices: $layoutService->getAuthenticationLayoutVariants(),
            default: $layoutService->getDefaultAuthenticationLayoutVariant(),
        );

        // write authentication layout changes
        $layoutService->configureAuthenticationLayout($authenticationLayoutSelected);
        $this->info($authenticationLayoutSelected . ' configured.');

        $applicationLayoutSelected = $this->choice(
            question: 'Which layout do you want to use for the application?',
            choices: $layoutService->getApplicationLayoutVariants(),
            default: $layoutService->getDefaultApplicationLayoutVariant(),
        );

        // write application layout changes
        $layoutService->configureApplicationLayout($applicationLayoutSelected);
        $this->info($applicationLayoutSelected . ' configured.');

        return self::SUCCESS;
    }
}
