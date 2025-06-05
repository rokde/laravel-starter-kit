<?php

declare(strict_types=1);

arch('Test the application architecture.')
    ->expect('App')
    ->toUseStrictTypes()
    ->toUseStrictEquality();

arch()->preset()->laravel()
    ->ignoring([
        App\Models\FrontMatter::class,
        App\Models\StaticPage::class,
    ]);
