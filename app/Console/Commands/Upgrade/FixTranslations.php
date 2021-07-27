<?php

namespace App\Console\Commands\Upgrade;

use App\Models\LanguageLine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class FixTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:fix-translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove outdated stuff, add new stuff';

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
     * The groups to delete
     *
     * @var array|string[]
     */
    protected array $groupsToDelete = ['home'];

    /**
     * The groups to update (reimport)
     *
     * @var array|string[]
     */
    protected array $groupsToUpdate = ['home'];

    /**
     * The specific keys to delete
     *
     * @var array|string[]
     */
    protected array $keys = [];


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Deleting old translations from database...');

        $groupsDeleted = LanguageLine::whereIn('group', $this->groupsToDelete)->delete();
        $keysDeleted = LanguageLine::whereIn('keys', $this->keys)->delete();
        $total = $groupsDeleted + $keysDeleted;

        $this->info("Removed {$total} old translations removed, re-importing...");

        Artisan::call('cache:clear');
        Artisan::call('translations:import', ['--only-groups' => implode(',', $this->groupsToUpdate)]);

        $this->info('All done.');
    }
}
