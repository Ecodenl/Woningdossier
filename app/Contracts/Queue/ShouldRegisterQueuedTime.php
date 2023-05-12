<?php

namespace App\Contracts\Queue;

interface ShouldRegisterQueuedTime
{
    public function registerQueuedTime();
}