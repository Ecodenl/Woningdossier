<?php

namespace App\Console\Commands;

use App\Models\Building;
use Illuminate\Console\Command;

class LegacyPrivateMessageCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:private-message-cleanup-deleted-buildings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove private messages for buildings that have already been deleted';

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
        $buildings = Building::withTrashed()->whereNotNull('deleted_at')->get();
        $this->line("Got " . $buildings->count() . " buildings that have been deleted");
        foreach($buildings as $building){
            $privateMessages  = $building->privateMessages;
            if ($privateMessages->count() > 0){
                $this->line("There is/are " . $privateMessages->count() . " private message(s) for building " .  $building->id);
                foreach($privateMessages as $privateMessage){
                    $this->info("Deleting private message " . $privateMessage->id . " for building " . $building->id);
                    $privateMessage->delete();
                }
            }
        }
    }
}
