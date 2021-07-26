<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;

class TranslationsToJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:translations-to-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Moves the translations from the translations table to its own respective table on the title or name column';

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
        //
    }
}
