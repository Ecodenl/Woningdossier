<?php

namespace App\Console\Commands\Fixes;

use App\Models\Building;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Matrix\Builder;

class AddMissingPrivateMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:add-missing-private-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'During the merge some private messages were left out, this command attempts to set them back';

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
        // approx 1608 rows, waar praten we over ?
        $missingPrivateMessages = PrivateMessage::withoutGlobalScopes()->hydrate(
            DB::table('sub_live.private_messages as sl_pm')
                ->whereNotExists(function ($query) {
                    $query
                        ->select('*')
                        ->from('db.private_messages as db_pm')
                        ->whereColumn('sl_pm.id', 'db_pm.id');
                })
                ->get()
                ->toArray()
        );

        $insertCount = 0;
        $fromUserDeleted = 0;
        $privateMessageAlreadyExistsCount = 0;
        foreach ($missingPrivateMessages as $missingPrivateMessage) {
            $shouldDoInsert = true;
            $building = $missingPrivateMessage->building()->withTrashed()->first();
            if (!$building instanceof Building) {
                // in this case a building is actually deleted (this just means we dont insert it)
                // the building is probably deleted due to some migration stuff during upgrade
                $shouldDoInsert = false;
            }

            // first we check if we are allowed to insert the row
            // then we check if there was a user id in the first place
            // and we check if he still exists
            // and if so we set it to null, because this is what normally would've happened
            if ($shouldDoInsert == true && !is_null($missingPrivateMessage->from_user_id) && !User::find($missingPrivateMessage->from_user_id) instanceof User) {
                $missingPrivateMessage->from_user_id = null;
                $fromUserDeleted++;
            }

            $privateMessageAlreadyExists = DB::table('private_messages')->find($missingPrivateMessage->id) instanceof \stdClass;
            if ($shouldDoInsert && !$privateMessageAlreadyExists) {
                $insertCount++;
                $privateMessage = $missingPrivateMessage->toArray();
                DB::table('private_messages')->insert($privateMessage);
            }
            if ($privateMessageAlreadyExists) {
                $privateMessageAlreadyExistsCount++;
            }
        }

        $this->info("A total of {$insertCount} private messages have been inserted");
        $this->info("A total of {$fromUserDeleted} from users have been deleted, these are set to null");
        $this->info("A total of {$privateMessageAlreadyExistsCount} private messages already existed, so these were updated.");

    }
}
