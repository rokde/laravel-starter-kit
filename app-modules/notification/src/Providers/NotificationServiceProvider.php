<?php

declare(strict_types=1);

namespace Modules\Notification\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\Contracts\NotificationRepository as NotificationRepositoryContract;
use Modules\Notification\Repositories\NotificationRepository;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/notification.php', 'notification');

        $this->app->bind(NotificationRepositoryContract::class, function (Application $app) {
            return new NotificationRepository($app['auth']->user());
        });
    }

    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/notification.php' => config_path('notification.php'),
                __DIR__.'/../../lang' => $this->app->langPath('vendor/notification'),
            ]);
        }
    }
}
