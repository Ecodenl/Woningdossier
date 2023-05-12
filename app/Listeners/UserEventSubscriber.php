<?php

namespace App\Listeners;

use App\Events\UserToolDataChanged;
use App\Services\UserService;
use Illuminate\Events\Dispatcher;

class UserEventSubscriber
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function handleToolDataChanged(UserToolDataChanged $event)
    {
        $this->userService->forUser($event->user)->toolChanged();
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            UserToolDataChanged::class => 'handleToolDataChanged',
        ];
    }
}
