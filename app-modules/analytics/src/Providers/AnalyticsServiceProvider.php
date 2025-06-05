<?php

declare(strict_types=1);

namespace Modules\Analytics\Providers;

use Illuminate\Support\ServiceProvider;
use Pan\PanConfiguration;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/analytics.php', 'analytics');

        PanConfiguration::maxAnalytics(config('analytics.max_analytics', 50));
        PanConfiguration::routePrefix(config('analytics.route_prefix', 'pan'));
        PanConfiguration::allowedAnalytics(config('analytics.allowed_analytics', []));
    }

    public function boot(): void {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__ . '/../../config/analytics.php' => config_path('analytics.php'),
        ]);
    }
}
