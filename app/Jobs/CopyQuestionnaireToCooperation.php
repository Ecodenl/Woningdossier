<?php

namespace App\Jobs;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
use App\Services\QuestionnaireService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CopyQuestionnaireToCooperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * The cooperation where the questionnaire needs to be copied to.
     *
     * @var Cooperation $cooperation
     */
    public $cooperation;

    /**
     * The questionnaire to copy.
     *
     * @var Questionnaire $questionnaire
     */
    public $questionnaire;

    /**
     * Create a new job instance.
     *
     * @param Cooperation $cooperation
     * @param Questionnaire $questionnaire
     *
     * @return void
     */
    public function __construct(Cooperation $cooperation, Questionnaire $questionnaire)
    {
        $this->cooperation = $cooperation;
        $this->questionnaire = $questionnaire;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        QuestionnaireService::copyQuestionnaireToCooperation($this->cooperation, $this->questionnaire);
    }
}
