<?php

namespace App\Console\Commands\Fixes;

use Illuminate\Console\Command;

class AddMissingPrivateMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:add-missing-private-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'During the merge some private messages were left out, this command attempts to set them back';

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
        $cooperations = [
            'deltawind' => [
                'private_messages' => 70000,
            ],

            'duec' => [
                'private_messages' => 100000,
            ],
            'lochemenergie' => [
                'private_messages' => 130000,
            ],

            'nhec' => [
                'private_messages' => 160000,
            ],

            'energiehuis' => [
                'private_messages' => 190000,
            ],

            'blauwvingerenergie' => [
                'private_messages' => 220000,
            ],

            'leimuidenduurzaam' => [
                'private_messages' => 250000,
            ],

            'wijdemeren' => [
                'private_messages' => 280000,
            ],
            'cnme' => [
                'private_messages' => 310000,
            ]
        ];
    }
}
