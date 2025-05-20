<?php

declare(strict_types=1);

namespace Modules\Notification\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Notification\Contracts\InAppNotification;

class InAppNotificationsRepository
{
    /**
     * @return Collection<[class: string; group: string; description: string;]>
     */
    public function all(): Collection
    {
        return collect(config('notification.notifications', []))
            ->map(function (string $notificationClass) {
                $isInAppNotification = $this->implements($notificationClass, InAppNotification::class);

                return [
                    'class' => $notificationClass,
                    'group' => $isInAppNotification ? $notificationClass::getGroup() : 'Default',
                    'description' => $isInAppNotification ? $notificationClass::getDescription() : Str::replace('\\', ' ', $notificationClass),
                ];
            });
    }

    private function implements(string $className, string $interfaceClass): bool
    {
        return in_array($interfaceClass, class_implements($className));
    }
}
