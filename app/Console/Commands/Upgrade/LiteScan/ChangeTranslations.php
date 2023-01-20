<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:change-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change translations.';

    public function handle()
    {
        DB::table('tool_questions')->where('short', 'insulation-wall-surface')
            ->update([
                'name' => json_encode([
                    'nl' => 'Te isoleren geveloppervlakte',
                ])
            ]);
        DB::table('tool_questions')->where('short', 'insulation-floor-surface')
            ->update([
                'name' => json_encode([
                    'nl' => 'Te isoleren vloeroppervlakte',
                ])
            ]);
    }
}