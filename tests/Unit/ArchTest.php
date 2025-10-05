<?php

declare(strict_types=1);

arch('Test the application architecture.')
    ->expect('App')
    ->toUseStrictTypes()
    ->toUseStrictEquality();

arch()->preset()->php();
//arch()->preset()->strict();
arch()->preset()->security();

// arch()->preset()->laravel()
//    ->ignoring([
//        App\Models\FrontMatter::class,
//        App\Models\StaticPage::class,
//    ]);
