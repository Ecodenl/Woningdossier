<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MergeUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:merge {userId1} {userId2}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge two users within the same cooperation. The first user has the \'truth\' whenever in doubt';

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
        $userId1 = (int) $this->argument('userId1');
        $userId2 = (int) $this->argument('userId2');
        $user1 = User::find($userId1);
        $user2 = User::find($userId2);

        if (! $user1 instanceof User) {
            $this->error('No user with ID '.$userId1);
            exit;
        }
        if (! $user2 instanceof User) {
            $this->error('No user with ID '.$userId2);
            exit;
        }
        if ($user1->cooperation_id !== $user2->cooperation_id) {
            $this->error('Users are not part of the same cooperation!');
            exit;
        }

        Log::warning(
            sprintf(
                'Merging users %s %s (%s) and %s %s (%s)',
                $user1->first_name, $user1->last_name, $user1->id,
                $user2->first_name, $user2->last_name, $user2->id
            )
        );

        $user = UserService::merge($user1, $user2);

//        Artisan::call('user:delete', ['user' => $userId2]);
        Log::info(sprintf('Users %s and %s were merged', $userId1, $userId2));
    }
}
