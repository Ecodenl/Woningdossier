<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Models\FileStorage;
use App\Models\FileType;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RemoveActionPlanReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:remove-action-plan-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete obsolete action plan report.';

    public function handle()
    {
        $fileTypesToDelete = FileType::whereIn('short', ['measure-report', 'measure-report-anonymized'])->get();

        foreach ($fileTypesToDelete as $fileTypeToDelete) {
            DB::table('file_storages')->where('file_type_id', $fileTypeToDelete->id)
                ->orderBy('id')->chunkById(100, function ($files) {
                    foreach ($files as $file) {
                        // If the file exists, delete it.
                        if (Storage::disk('downloads')->exists($file->filename)) {
                            Storage::disk('downloads')->delete($file->filename);
                        }
                    }
                });

            // Delete file type. File storages will auto-cascade.
            $fileTypeToDelete->delete();
        }
    }
}