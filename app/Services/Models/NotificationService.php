<?php

namespace App\Services\Models;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Notification;
use App\Traits\FluentCaller;

class NotificationService
{
    use FluentCaller;

    protected InputSource $inputSource;
    protected Building $building;
    protected string $type;

    public function forInputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;

        return $this;
    }

    public function forBuilding(Building $building): self
    {
        $this->building = $building;

        return $this;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isActive(): bool
    {
        return (bool) optional($this->getNotification())->is_active;
    }

    public function setActive(int $count = 1)
    {
        Notification::allInputSources()->updateOrCreate(
            [
                'input_source_id' => $this->inputSource->id,
                'type' => $this->type,
                'building_id' => $this->building->id,
            ],
            [
                'is_active' => true,
                'count' => $count,
            ]
        );
    }

    public function deactivate()
    {
        $notification = $this->getNotification();
        $notification->active_count--;
        if ($notification->active_count === 0) {
            $notification->is_active = false;
        }
        $notification->save();
    }

    private function getNotification(): ?Notification
    {
        return Notification::forBuilding($this->building)
            ->forType($this->type)
            ->forInputSource($this->inputSource)
            ->first();
    }
}