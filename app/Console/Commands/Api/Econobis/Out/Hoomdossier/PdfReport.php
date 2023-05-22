<?php

namespace App\Console\Commands\Api\Econobis\Out\Hoomdossier;

use App\Jobs\Econobis\Out\SendPdfReportToEconobis;
use App\Models\FileStorage;
use App\Models\FileType;
use App\Models\InputSource;
use App\Models\Integration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Log;

class PdfReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:econobis:out:hoomdossier:pdf-report
    {--interval= : A custom interval, in minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the PDF to Econobis for all buildings that either haven\'t send it yet, or have changed.';

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
        $interval = $this->option('interval');
        $interval = is_numeric($interval) ? (int)$interval : null;
        $interval = is_null($interval) ? config("hoomdossier.services.econobis.interval.".SendPdfReportToEconobis::class) : $interval;
        $datetime = Carbon::now()->subMinutes($interval);

        // Applies available_until global scope
        FileStorage::select([
            'file_storages.id as file_storage_id',
            'file_storages.input_source_id',
            'file_storages.building_id'
        ])
            ->join('buildings AS b', 'file_storages.building_id', '=', 'b.id')
            ->join('users AS u', 'b.user_id', '=', 'u.id')
            ->leftJoin('integration_processes AS ip', function (JoinClause $join) {
                $join->on('b.id', '=', 'ip.building_id')
                    ->where('ip.process', SendPdfReportToEconobis::class)
                    ->where('integration_id', Integration::findByShort('econobis')->id);
            })
            ->forInputSource(InputSource::coach())
            ->whereNotNull('u.extra->contact_id')
            ->where('u.allow_access', 1)
            ->where('file_storages.file_type_id', FileType::findByShort('pdf-report')->id)
            ->where(function ($query) {
                $query->whereNull('ip.synced_at')
                    ->orWhereRaw('file_storages.updated_at > ip.synced_at');
            })
            ->where('file_storages.updated_at', '<=', $datetime)
            ->with('building')
            ->chunkById(50, function ($fileStorages) {
                foreach ($fileStorages as $fileStorage) {
                    Log::debug("Sending PDF report to Econobis for building {$fileStorage->building_id}");
                    SendPdfReportToEconobis::dispatch($fileStorage->building);
                }
            }, FileStorage::getModel()->getTable().'.'.FileStorage::getModel()->getKeyName(), 'file_storage_id');

        return 0;
    }
}
