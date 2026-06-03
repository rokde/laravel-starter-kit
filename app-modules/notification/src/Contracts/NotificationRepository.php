<?php

declare(strict_types=1);

namespace Modules\Notification\Contracts;

use Illuminate\Support\Collection;
use Modules\Notification\DataTransferObjects\Notification;

interface NotificationRepository
{
    /**
     * @return Collection<Notification>|Notification[]
     */
    public function all(): Collection;
}
