<?php

namespace App\Traits\Queue;

use App\Helpers\Str;
use App\Services\Models\NotificationService;

trait HasNotifications
{
    public string $uuid;
    public bool $caresForInputSource = true;

    protected function setUuid(): void
    {
        $this->uuid = Str::uuid();
    }

    protected function ignoreNotificationInputSource(): void
    {
        $this->caresForInputSource = false;
    }

    protected function deactivateNotification(): void
    {
        NotificationService::init()
            ->forBuilding($this->building ?? $this->user->building)
            ->forInputSource($this->inputSource)
            ->setType(get_class($this))
            ->setUuid($this->uuid)
            ->deactivate();
    }
}
