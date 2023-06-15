<?php

namespace App\Console\Commands\Macros;

use App\Helpers\Arr;
use App\Helpers\FileFormats\CsvHelper;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * NOTE;
 * This command will change, since the given csvs will not and never will be anywhere near consistent.
 * DO NOT load up the contact csv file and run it blindly.
 */
class UpdateContactIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'macros:update-contact-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update contact IDs in the database using file in storage';

    public function handle()
    {
        if (! Storage::disk('local')->exists('contact-ids.csv')) {
            $this->error('"contact-ids.csv" not found at /storage/app!');
            exit;
        }

        $changes = CsvHelper::toArray(Storage::disk('local')->path('contact-ids.csv'), ';', false);
        foreach ($changes as $data) {
            $cooperationName = $data[0];
            $accountId = $data[1];
            $newContactId = $data[2];

            $cooperation = Cooperation::where('slug', 'gouda')->first();
            $user = User::forMyCooperation($cooperation->id)->where('account_id', $accountId)->first();

            if ($user instanceof User) {
                $extra = $user->extra ?? [];
                $oldContactId = Arr::get($extra, 'contact_id', 'No old');
                Log::info("Changing the contact ID of user {$user->id} from {$oldContactId} to {$newContactId}");

                $extra['contact_id'] = $newContactId;

                $user->update([
                    'extra' => $extra,
                ]);
            }
        }
    }
}