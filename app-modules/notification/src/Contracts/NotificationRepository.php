<?php

namespace Modules\Notification\Contracts;

use Illuminate\Support\Collection;

interface NotificationRepository
{
    /**
     * @return Collection<\Modules\Notification\DataTransferObjects\Notification>|\Modules\Notification\DataTransferObjects\Notification[]
     */
    public function all(): Collection;
}
