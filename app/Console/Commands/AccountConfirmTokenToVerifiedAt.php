<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AccountConfirmTokenToVerifiedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'account:confirm-token-to-verified-at';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates the confirm token to the verified at';

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
        Account::whereNull('confirm_token')->update(['email_verified_at' => Carbon::now()]);
    }
}
