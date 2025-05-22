<?php

declare(strict_types=1);

namespace Modules\Todo\Providers;

use Illuminate\Support\ServiceProvider;

class TodoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register bindings
    }

    public function boot(): void
    {
        // Bootstrap the module
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'todo');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'todo');

        // Register Inertia components
//        $this->registerInertiaComponents();
    }

    /**
     * Register Inertia components.
     */
    private function registerInertiaComponents(): void
    {
        $this->app->booted(function (): void {
            $manager = $this->app->make(\Inertia\ResponseFactory::class);

            $manager->share('todo', fn () => [
                'routes' => [
                    'index' => route('todos.index'),
                    'create' => route('todos.create'),
                    'store' => route('todos.store'),
                ],
            ]);

            $manager->rootView('app');

            $manager->version(fn () => md5_file(public_path('mix-manifest.json') ?? ''));
        });
    }
}
