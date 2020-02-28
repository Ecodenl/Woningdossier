<?php

namespace App\Console\Commands;

use App\Mail\RequestAccountConfirmationEmail;
use App\Mail\ResetPasswordRequest;
use App\Mail\UnreadMessagesEmail;
use App\Mail\UserAssociatedWithCooperation;
use App\Mail\UserChangedHisEmail;
use App\Mail\UserCreatedEmail;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Console\Command;

class TestMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test mails';

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
//        $u = User::find(1);
//        $c = Cooperation::find(1);

//        \Mail::to('demo@example.org')->sendNow(
//            new RequestAccountConfirmationEmail($u, $c)
//        );
//        \Mail::to('demo@example.org')->sendNow(
//            new ResetPasswordRequest($c, $u->account, 'afkhjashfjkashfjkshjksfhjskf')
//        );
//        \Mail::to('demo@example.org')->sendNow(
//            new UnreadMessagesEmail($u, $c, 19)
//        );
//        \Mail::to('demo@example.org')->sendNow(
//            new UserAssociatedWithCooperation($c, $u)
//        );
//        \Mail::to('demo@example.org')->sendNow(
//            new UserChangedHisEmail($u, $u->account, 'testnewmail@example.org', 'demo@example.org')
//        );
//        \Mail::to('demo@example.org')->sendNow(
//            new UserCreatedEmail($c, $u, 'sdfkhasgdfuiasdgfyu')
//        );


    }
}
