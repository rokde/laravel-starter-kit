<?php

declare(strict_types=1);

namespace Modules\Passkey\Providers;

use Illuminate\Support\ServiceProvider;

class PasskeyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->loadJsonTranslationsFrom(__DIR__.'/../../lang');
    }
}
