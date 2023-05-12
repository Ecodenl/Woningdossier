<?php

namespace App\Console\Commands\Macros;

use App\Helpers\FileFormats\CsvHelper;
use App\Models\Building;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            // Not using associative headers because misspellings...
            $buildingId = $data[2];
            $newContactId = $data[0];

            $building = Building::find($buildingId);
            $user = $building->user ?? null;

            if ($user instanceof User) {
                $extra = $user->extra ?? [];
                $oldContactId = data_get($extra, 'contact_id');
                Log::info("Changing the contact ID of user {$user->id} (building {$buildingId}) from {$oldContactId} to {$newContactId}");

                $extra['contact_id'] = $newContactId;

                $user->update([
                    'extra' => $extra,
                ]);
            } else {
                Log::warning("No user found for building {$buildingId}");
            }
        }
    }
}