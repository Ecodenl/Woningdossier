<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class LegacySetReadStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:set-read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set all private messages to read from users who have more than 1 role with the resident role';

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
        $residentRole = Role::findByName('resident');

        // we will use this as reference in case stuff goes south
        $timestamp = Carbon::createFromFormat('Y-m-d h:i:s', '2020-12-21 00:00:00');

        $residentsWithMoreRoles = User::whereHas('roles', function ($q) use ($residentRole) {
            $q->where('role_id', $residentRole->id);
        })->has('roles', '>', 1)
            ->with(['building' => function ($query) {
                $query->with('privateMessages');
            }])
            ->get();


        foreach ($residentsWithMoreRoles as $user) {
            $privateMessages = $user->building->privateMessages;
            foreach ($privateMessages as $privateMessage) {
                $this->info("b_id: {$user->building->id} pm_id: {$privateMessage->id} to read. .");

                $privateMessage
                    ->privateMessageViews()
                    ->whereNull('read_at')
                    ->update([
                        'read_at' => $timestamp,
                    ]);
            }
        }

        $this->info('Set messages to read');
    }
}
