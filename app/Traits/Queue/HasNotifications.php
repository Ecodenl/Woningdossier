<?php

namespace App\Traits\Queue;

use App\Helpers\Str;
use App\Services\Models\NotificationService;

trait HasNotifications
{
    public string $uuid;
    public bool $caresForInputSource = true;

    protected function setUuid()
    {
        $this->uuid = Str::uuid();
    }

    protected function ignoreNotificationInputSource()
    {
        $this->caresForInputSource = false;
    }

    protected function deactivateNotification()
    {
        NotificationService::init()
            ->forBuilding($this->building ?? $this->user->building)
            ->forInputSource($this->inputSource)
            ->setType(get_class($this))
            ->setUuid($this->uuid)
            ->deactivate();
    }
}