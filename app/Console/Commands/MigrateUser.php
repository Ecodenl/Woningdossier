<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Cooperation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:user 
                                {email : The email of the user you would like to migrate}
                                {from  : The cooperation id, which should be migrated  }
                                {to  : The cooperation ID to which the user should be migrated  }';

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
     * @return int
     */
    public function handle()
    {
        $fromCooperation = Cooperation::findOrFail($this->argument('from'));
        $toCooperation   = Cooperation::findOrFail($this->argument('to'));
        $account         = Account::withoutGlobalScopes()->where('email', $this->argument('email'))->first();

        if ($account instanceof Account) {
            $userAlreadyMemberOfCooperation = $account->users()->where('cooperation_id', $toCooperation->id)->exists();
            if ($userAlreadyMemberOfCooperation) {
                $this->error("Account already has a user that is member of the cooperation [{$account->email}]");
            } else {
                $userId = DB::table('users')
                    ->where('account_id', $account->id)
                    ->where('cooperation_id', $fromCooperation->id)
                    ->first()->id;

                $buildingId = DB::table('buildings')
                    ->where('user_id', $userId)
                    ->first()->id;

                $this->warn("[{$account->email}]");
                $this->info("Moving the user from {$fromCooperation->name} to {$toCooperation->name}");
                // First move the user from the old to its new cooperation
                DB::table('users')
                    ->where('id', $userId)
                    ->where('cooperation_id', $fromCooperation->id)
                    ->update(['cooperation_id' => $toCooperation->id]);

                $pdfFileType = DB::table('file_types')
                    ->where('short', '=', 'pdf-report')
                    ->first();

                $this->info("Moving the file storages from {$fromCooperation->name} to {$toCooperation->name}");
                DB::table('file_storages')
                    ->where('building_id', $buildingId)
                    ->where('file_type_id', $pdfFileType->id)
                    ->update(['cooperation_id' => $toCooperation->id]);


                $this->info("Deleting the private_message_views {$fromCooperation->name}");
                // THIS HAS to be done before the private messages itself, otherwise the where is not valid.
                // and we will delete the unread messages, since the coach connection is lost it would be a gigantic mess to fix.
                DB::table('private_message_views')
                    ->join('private_messages', 'private_messages.id', '=', 'private_message_views.private_message_id')
                    ->where('private_messages.building_id', $buildingId)
                    ->where(function ($query) use ($fromCooperation) {
                        $query->where('private_messages.from_cooperation_id', $fromCooperation->id)
                            ->orWhere('private_messages.to_cooperation_id', $fromCooperation->id);
                    })->delete();

                $this->info("Moving the private message send FROM the cooperation from {$fromCooperation->name} to {$toCooperation->name}");
                DB::table('private_messages')
                    ->where('private_messages.building_id', $buildingId)
                    ->where('private_messages.from_cooperation_id', $fromCooperation->id)
                    ->update([
                        'private_messages.from_cooperation_id' => $toCooperation->id,
                    ]);
                $this->info("Moving the private message send TO the cooperation from {$fromCooperation->name} to {$toCooperation->name}");
                DB::table('private_messages')
                    ->where('private_messages.building_id', $buildingId)
                    ->where('private_messages.to_cooperation_id', $fromCooperation->id)
                    ->update([
                        'private_messages.to_cooperation_id' => $toCooperation->id,
                    ]);


                $this->info("Deleting the coach access, building_permissions and building_coach statusses.");
                // now delete all the building coach statuses on the building
                DB::table('building_coach_statuses')
                    ->where('building_id', $buildingId)
                    ->delete();

                // and ofcourse the buildin permissions ass wwell
                DB::table('building_permissions')
                    ->where('building_id', $buildingId)
                    ->delete();
            }
        } else {
            $this->error("No account found for email [{$this->argument('email')}]");
        }


        return 0;
    }
}
