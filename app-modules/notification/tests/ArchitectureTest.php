<?php

declare(strict_types=1);

arch('Test the domain module boundaries for the notification module.')
    ->expect('Modules\Notification')
    ->toOnlyBeUsedIn('Modules\Notification')
    ->ignoring([
        'Modules\Notification\Contracts',
        'Modules\Notification\DataTransferObjects',
        'Modules\Notification\Notifications\Concerns',
    ]);
