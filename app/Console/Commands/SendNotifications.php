<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications {type : Notification type to be send, if left empty all notification types will be sent.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the user notifications based on their interval and last_send_on';

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
        //
    }
}
