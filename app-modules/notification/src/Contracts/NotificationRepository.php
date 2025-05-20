<?php

declare(strict_types=1);

namespace Modules\Notification\Contracts;

use Illuminate\Support\Collection;

interface NotificationRepository
{
    /**
     * @return Collection<\Modules\Notification\DataTransferObjects\Notification>|\Modules\Notification\DataTransferObjects\Notification[]
     */
    public function all(): Collection;
}
