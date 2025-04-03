<?php

namespace App\Console\Commands;

use App\Models\FileStorage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredFileStorages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'file-storages:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will delete all file storages with its files that are considered to be expired.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Fetch 50 file storages that have expired and delete them. This CRON runs every 30 minutes, so
        // eventually only available file storages will remain. This is done on purpose to balance the load.
        $fileStorages = FileStorage::forAllCooperations()->allInputSources()->expired()->limit(50)->get();
        foreach ($fileStorages as $fileStorage) {
            if (Storage::disk('downloads')->exists($fileStorage->filename)) {
                Storage::disk('downloads')->delete($fileStorage->filename);
            }
            $fileStorage->delete();
        }

        return self::SUCCESS;
    }
}
