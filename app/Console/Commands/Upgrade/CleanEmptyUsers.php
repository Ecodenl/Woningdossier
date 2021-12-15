<?php

namespace App\Console\Commands\Upgrade;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class CleanEmptyUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:clean-empty-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $users = User::withoutGlobalScopes()->whereIn('account_id', [10830, 303])->get();

        foreach ($users as $user) {
            UserService::deleteUser($user, true);
        }
    }
}
