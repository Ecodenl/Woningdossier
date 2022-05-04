<?php

namespace App\Console\Commands;

use App\Jobs\RecalculateStepForUser;
use App\Models\Notification;
use Illuminate\Console\Command;

class TransformRecalculateNotificationType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transform:recalculate-notification-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Previously the type of a notification was "recalculate" we rather store the job class';

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
        Notification::allInputSources()->where('type', 'recalculate')->update(['type' => RecalculateStepForUser::class]);
    }
}
