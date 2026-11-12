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

    public function deactivateNotification(): void
    {
        $service = NotificationService::init()
            ->forBuilding($this->building ?? $this->user->building)
            ->setType(get_class($this))
            ->setUuid($this->uuid);

        // The job might not care about the input source, in which case the notification was set without one, and
        // so we must not query on it.
        if ($this->caresForInputSource) {
            $service->forInputSource($this->inputSource);
        }

        $service->deactivate();
    }
}
