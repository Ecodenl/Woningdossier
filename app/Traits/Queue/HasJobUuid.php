<?php

namespace App\Traits\Queue;

use Illuminate\Support\Str;

trait HasJobUuid
{
    public ?string $jobUuid = null;

    public function setJobUuid(): void
    {
        $this->jobUuid = Str::uuid();
    }

    public function getJobUuid(): string
    {
        if (is_null($this->jobUuid)) {
            $this->setJobUuid();
        }
        return $this->jobUuid;
    }
}