<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateToolQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool-questions:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update tool questions';

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
        $count = DB::table('tool_questions')
            ->where('short', 'amount-electricity')
            ->update([
                'validation' => ['required', 'numeric', 'integer', 'min:-10000', 'max:25000'],
            ]);
        $this->info("A total of {$count} tool questions have been updated");
    }
}
