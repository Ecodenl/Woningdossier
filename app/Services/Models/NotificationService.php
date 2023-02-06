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
    protected string $uuid;

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

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function hasActiveTypes(array $types): bool
    {
        $isActive = false;
        foreach ($types as $type) {
            if ($this->setType($type)->isActive()) {
                $isActive = true;
                break;
            }
        }

        return $isActive;
    }

    public function isActive(): bool
    {
        return $this->getNotification() instanceof Notification;
    }

    public function isNotActive(): bool
    {
        return ! $this->isActive();
    }

    public function setActive(array $uuids)
    {
        foreach ($uuids as $uuid) {
            Notification::allInputSources()->updateOrCreate(
                [
                    'input_source_id' => $this->inputSource->id,
                    'type' => $this->type,
                    'uuid' => $uuid,
                    'building_id' => $this->building->id,
                ],
            );
        }
    }

    /**
     * Deactivate the notification.
     *
     * @return void
     */
    public function deactivate()
    {
        optional($this->getNotification())->delete();
    }

    protected function getNotification(): ?Notification
    {
        $query = Notification::activeNotifications($this->building, $this->inputSource)
            ->forType($this->type);

        if (isset($this->uuid)) {
            $query->forUuid($this->uuid);
        }

        return $query->first();
    }
}