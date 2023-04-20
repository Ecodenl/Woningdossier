<?php

namespace App\Console\Commands;

use App\Models\FileStorage;
use App\Services\DiscordNotifier;
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
        // so this withExpired thing is accurate at the moment
        // however the FileStorage::isProcessed set the available_until, which defaults to seven days if not set by file type.
        // currently this is not a problem, no file type has a specific available.
        FileStorage::withoutGlobalScopes()->withExpired()->chunkById(250, function ($fileStorages) {
            foreach ($fileStorages as $fileStorage) {
                if (Storage::disk('downloads')->exists($fileStorage->filename)) {
                    Storage::disk('downloads')->delete($fileStorage->filename);
                    $fileStorage->delete();
                }
            }
        });

        return 0;
    }
}
