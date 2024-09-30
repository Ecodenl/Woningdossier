<?php

namespace App\Console\Commands\Monitoring;

use App\Helpers\Queue;
use App\Models\QueueLog;
use App\Services\DiscordNotifier;
use Carbon\Carbon;
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
    public function handle(): int
    {
        // Log queue size for each available queue.
        foreach (Queue::getQueueNames() as $queueName) {
            $size = QueueFacade::size($queueName);

            $log = QueueLog::create([
                'queue' => $queueName,
                'size' => $size,
            ]);

            $warningSize = config('hoomdossier.queue.warning_size');
            if ($size >= $warningSize) {
                // Previous log
                $prevLog = QueueLog::orderByDesc('created_at')->where('queue', $queueName)
                    ->where('created_at', '!=', $log->created_at)
                    ->first();

                if (! $prevLog instanceof QueueLog || $prevLog->size < $warningSize) {
                    DiscordNotifier::init()
                        ->notify("Queue {$queueName} has reached {$warningSize}!");
                }
            }
        }

        // Remove logs older than 7 days.
        QueueLog::whereIn('queue', Queue::getQueueNames())
            ->where('created_at', '<=', Carbon::now()->subDays(7))
            ->delete();

        return 0;
    }
}
