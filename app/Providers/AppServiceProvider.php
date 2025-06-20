<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerTestingInertiaWithModuleSupport();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::macro('inApplicationTimezone', fn () => $this->tz(config('app.timezone_display', 'UTC')));

        Carbon::macro('inUserTimezone', fn () => $this->tz(auth()->user()?->timezone ?? config('app.timezone_display', 'UTC')));
    }

    private function registerTestingInertiaWithModuleSupport(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        $this->app->bind('inertia.testing.view-finder', function (Application $app): FileViewFinder {
            $fileViewFinder = new FileViewFinder(
                $app['files'],
                [resource_path('js/pages')],
                ['vue']
            );

            // we need to add the namespace hints for loading "[module]::[View].vue"
            collect(File::directories(base_path('app-modules')))
                ->filter(fn (string $modulePath) => File::exists($modulePath.'/resources/js/pages'))
                ->map(fn (string $modulePath): array => [
                    'namespace' => basename($modulePath),
                    'pages' => $modulePath.'/resources/js/pages',
                ])
                ->each(fn (array $module) => $fileViewFinder->addNamespace($module['namespace'], $module['pages']));

            return $fileViewFinder;
        });
    }
}
