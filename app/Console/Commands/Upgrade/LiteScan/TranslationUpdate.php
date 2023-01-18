<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class TranslationUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:translation-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete completed step due to new sub step.';

    public function handle()
    {
        if (DB::table('language_lines')->where('group', 'cooperation/frontend/tool')
            ->where('key', 'my-plan.calculations.description')->doesntExist()
        ) {
            DB::table('language_lines')
                ->where('group', 'my-plan')
                ->where('key', 'download.title')
                ->update([
                    'group' => 'cooperation/frontend/tool',
                    'key' => 'my-plan.downloads.create-report',
                ]);

            DB::table('language_lines')
                ->where('group', 'my-plan')
                ->where('key', 'warnings.ventilation')
                ->update([
                    'group' => 'cooperation/tool/ventilation',
                    'key' => 'calculations.warning',
                ]);

            DB::table('language_lines')
                ->where('group', 'my-plan')
                ->delete();


            DB::table('language_lines')
                ->insert([
                    'group' => 'cooperation/frontend/tool',
                    'key' => 'my-plan.calculations.description',
                    'text' => json_encode([
                        'nl' => __('cooperation/frontend/tool.my-plan.calculations.description'),
                    ])
                ]);

            Artisan::call('cache:clear');
        }
    }
}