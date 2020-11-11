<?php

namespace App\Console\Commands\Tool;

use App\Models\User;
use Illuminate\Console\Command;

class RecalculateForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:recalculate {user* : The ID\'s of the users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the tool for a given user, will create new user action plan advices.';

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
        $users = User::findMany($this->argument('user'));
        foreach ($users as $user) {

        }
    }
}
