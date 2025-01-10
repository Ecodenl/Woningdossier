<?php

namespace App\Services\Models;

use App\Models\InputSource;
use App\Models\Notification;
use App\Traits\FluentCaller;
use App\Traits\Services\HasBuilding;
use App\Traits\Services\HasInputSources;

class NotificationService
{
    use FluentCaller,
        HasBuilding,
        HasInputSources;

    protected string $type;
    protected string $uuid;

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
        // TODO: Should we "reset" the type to the type we had before calling this method?
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

    public function setActive(array $uuids = [])
    {
        foreach ($uuids as $uuid) {
            Notification::allInputSources()->updateOrCreate(
                [
                    'input_source_id' => $this->inputSource?->id,
                    'type' => $this->type,
                    'uuid' => $uuid,
                    'building_id' => $this->building->id,
                ],
            );
        }
    }

    /**
     * Deactivate the notification.
     */
    public function deactivate(): void
    {
        $this->getNotification()?->delete();
    }

    protected function getNotification(): ?Notification
    {
        $query = Notification::forBuilding($this->building)
            ->forType($this->type);

        // Master gets created automatically due to GetMyValuesTrait, even if input source is null. Therefore, if you
        // explicitly want to check for master, ensure so via static::forInputSource, otherwise we will ignore master,
        // so we get the row with non-master/null input source (since even when deleting a null input source row, it
        // will delete the master due to uuid).
        $this->inputSource instanceof InputSource ? $query->forInputSource($this->inputSource) : $query->allInputSources()->where(function ($query) {
                // LME: MySQL treats NULL not as undefined but as unknown. When we query "not equals", the
                // values that are NULL are not returned, as MySQL is not sure if it matches or not. This is a failsafe.
                // By querying as OR on the same column, we get the required result. We could also use the null safe
                // operator ( <=> ), but that reads awkward.
                $query->where('input_source_id', '!=', InputSource::master()->id)
                    ->orWhereNull('input_source_id');
        });

        if (isset($this->uuid)) {
            $query->forUuid($this->uuid);
        }

        return $query->first();
    }
}
