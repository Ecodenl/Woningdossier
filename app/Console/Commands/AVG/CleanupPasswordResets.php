<?php

namespace App\Console\Commands\AVG;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupPasswordResets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avg:cleanup-password-resets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup the old password resets';

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
        // Expire is in minutes.
        $expires = config('auth.passwords.users.expire');

        $hasExpired = Carbon::now()->subMinutes($expires);

        DB::table('password_resets')->where('created_at', '<', $hasExpired)->delete();
    }
}