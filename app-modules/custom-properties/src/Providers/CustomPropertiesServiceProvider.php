<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class CustomPropertiesServiceProvider extends ServiceProvider
{
    public function register(): void {
        Blueprint::macro('customProperties', function () {
            $this->json('custom_properties')->nullable()->default(null);
        });

        Blueprint::macro('dropCustomProperties', function () {
            $this->dropColumn('custom_properties');
        });
    }

    public function boot(): void {}
}
