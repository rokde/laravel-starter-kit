<?php

namespace Modules\Notification\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Modules\Notification\Contracts\NotificationRepository as NotificationRepositoryContract;
use Modules\Notification\Repositories\NotificationRepository;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationRepositoryContract::class, function (Application $app) {
            return new NotificationRepository($app['auth']->user());
        });
    }

    public function boot(): void
    {
    }
}
