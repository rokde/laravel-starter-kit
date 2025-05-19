<?php

namespace Modules\Notification\Contracts;

interface InAppNotification
{
    public function getUrl(): ?string;

    public function getTitle(): string;

    public static function getDescription(): string;

    public static function getGroup(): string;
}
