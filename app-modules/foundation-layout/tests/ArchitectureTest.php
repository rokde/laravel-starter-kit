<?php

declare(strict_types=1);

arch('Test the domain module boundaries for the foundation-layout module.')
    ->expect('Modules\FoundationLayout')
    ->toOnlyBeUsedIn('Modules\FoundationLayout')
    ->ignoring([
        'Modules\FoundationLayout\Data',
        'Modules\FoundationLayout\Variants',
    ]);
