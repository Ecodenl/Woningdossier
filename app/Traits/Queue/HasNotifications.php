<?php

namespace App\Traits\Queue;

use App\Helpers\Str;
use App\Services\Models\NotificationService;

trait HasNotifications
{
    public string $uuid;

    protected function setUuid()
    {
        $this->uuid = Str::uuid();
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