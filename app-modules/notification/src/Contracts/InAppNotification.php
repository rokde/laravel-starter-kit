<?php

declare(strict_types=1);

namespace Modules\Notification\Contracts;

interface InAppNotification
{
    public static function getDescription(): string;

    public static function getGroup(): string;

    public function getUrl(): ?string;

    public function getTitle(): string;
}
