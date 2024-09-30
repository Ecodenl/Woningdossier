<?php

namespace App\Jobs;

use App\Models\InputSource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Bus\Dispatchable;

class ResetDossierForUser
{
    use Dispatchable;

    public User $user;
    public InputSource $inputSource;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, InputSource $inputSource)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
    }

    /**
     * Execute the job.
     */
    public function handle(UserService $userService): void
    {
        $userService->forUser($this->user)->resetUser($this->inputSource);
    }
}
