<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class DeleteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:delete {user : User ID or range (e.g. 10-15)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete one or a range of users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Note: the range cannot contain spaces like "1 - 2" as this would
        // result in an error on the command: it would see multiple arguments.
        $userIds = trim($this->argument('user'));

        if (false === stristr($userIds, '-')) {
            $userIds = [(int) $userIds];
        } else {
            list($from, $to) = explode('-', $userIds);
            $userIds = range($from, $to);
        }

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user instanceof User) {
                \Log::warning('Deleting user '.$user->id);
                UserService::deleteUser($user);
                \Log::info('User '.$user->id.' was deleted');
                $this->info('User '.$user->id.' was deleted');
            }
        }

        return;
    }
}
