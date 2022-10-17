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

    public function isNotActive(): bool
    {
        return ! $this->isActive();
    }

    public function setActive(int $count = 1)
    {
        // Get current count. This is because we could have 2 RecalculateForUser processes being dispatched at the
        // same time. It wouldn't make sense to show the action plan if one process is still running.
        $count = $count + (optional($this->getNotification())->active_count ?? 0);
        $active = $count > 0;

        Notification::allInputSources()->updateOrCreate(
            [
                'input_source_id' => $this->inputSource->id,
                'type' => $this->type,
                'building_id' => $this->building->id,
            ],
            [
                'is_active' => $active,
                'active_count' => $count,
            ]
        );
    }

    /**
     * Decrement the active count by one, and deactivate the notification if the count reaches 0.
     *
     * @param bool $force If true the notification will be deactivated regardless of count.
     *
     * @return void
     */
    public function deactivate(bool $force = false)
    {
        $notification = $this->getNotification();

        // If there's no notification there's nothing to deactivate
        if ($notification instanceof Notification) {
            if ($force) {
                $notification->update([
                    'active_count' => 0,
                    'is_active' => false,
                ]);
            } else {
                $notification->active_count--;
                if ($notification->active_count === 0) {
                    $notification->is_active = false;
                }

                $notification->save();
            }
        }
    }

    protected function getNotification(): ?Notification
    {
        return Notification::forBuilding($this->building)
            ->forType($this->type)
            ->forInputSource($this->inputSource)
            ->first();
    }
}