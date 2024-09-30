<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Services\QuestionnaireService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CopyQuestionnaireToCooperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The cooperation where the questionnaire needs to be copied to.
     *
     * @var Cooperation
     */
    public $cooperation;

    /**
     * The questionnaire to copy.
     *
     * @var Questionnaire
     */
    public $questionnaire;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $this->queue = Queue::APP;
        $this->cooperation = $cooperation;
        $this->questionnaire = $questionnaire;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        QuestionnaireService::copyQuestionnaireToCooperation($this->cooperation, $this->questionnaire);
    }
}
