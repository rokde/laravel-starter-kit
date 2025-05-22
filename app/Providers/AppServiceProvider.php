<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;
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
        $this->aggresivePrefetching();
        $this->automaticallyEagerLoadRelationships();
        $this->modelsShouldBeStrict();
        $this->unguardModels();
        $this->fakeSleep();
        $this->forceHttps();
        $this->immutableDates();
        $this->preventStrayRequests();
        $this->prohibitDestructiveCommands();
        $this->useStrongPassword();
    }

    private function aggresivePrefetching(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function automaticallyEagerLoadRelationships(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }

    private function fakeSleep(): void
    {
        Sleep::fake(app()->runningUnitTests());
    }

    private function forceHttps(): void
    {
        URL::forceHttps(app()->isProduction());
    }

    private function immutableDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function preventStrayRequests(): void
    {
        Http::preventStrayRequests(app()->runningUnitTests());
    }

    private function prohibitDestructiveCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function useStrongPassword(): void
    {
        Password::defaults(
            fn (): ?Password => app()->isProduction() ? Password::min(12)->max(255)->uncompromised() : null
        );
    }

    private function modelsShouldBeStrict(): void
    {
        Model::shouldBeStrict(! app()->runningUnitTests());
    }

    private function unguardModels(): void
    {
        Model::unguard();
    }

    private function registerTestingInertiaWithModuleSupport(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        $this->app->bind('inertia.testing.view-finder', function ($app) {
            $fileViewFinder = new FileViewFinder(
                $app['files'],
                [resource_path('js/pages')],
                ['vue']
            );

            // we need to add the namespace hints for loading "[module]::[View].vue"
            collect(File::directories(base_path('app-modules')))
                ->filter(fn (string $modulePath) => File::exists($modulePath.'/resources/js/pages'))
                ->map(function (string $modulePath) {
                    return [
                        'namespace' => basename($modulePath),
                        'pages' => $modulePath.'/resources/js/pages',
                    ];
                })
                ->each(fn (array $module) => $fileViewFinder->addNamespace($module['namespace'], $module['pages']));

            return $fileViewFinder;
        });
    }
}
