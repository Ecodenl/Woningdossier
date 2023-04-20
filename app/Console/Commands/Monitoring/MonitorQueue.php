<?php

namespace App\Console\Commands\Monitoring;

use App\Helpers\Queue;
use App\Models\QueueLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue as QueueFacade;

class MonitorQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitoring:monitor-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a queue log for the size of every queue at this moment.';

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
        foreach (Queue::getQueueNames() as $queueName) {
            QueueLog::create([
                'queue' => $queueName,
                'size' => QueueFacade::size($queueName),
            ]);
        }

        return 0;
    }
}
