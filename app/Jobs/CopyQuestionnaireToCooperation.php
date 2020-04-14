<?php

namespace App\Jobs;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\QuestionOption;
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
        $questionnaire = $this->questionnaire;
        $cooperationId = $this->cooperation->id;

        /** @var Questionnaire $questionnaireToReplicate */
        $questionnaireToReplicate = $questionnaire->replicate();

        // for now this will do it as there is only one translation which is dutch.
        // we MUST create a new translation because this will generate a new record in the translations table
        // this way each cooperation can edit the question names without messing up the other cooperation its questionnaires.
        $questionnaireToReplicate->cooperation_id = $cooperationId;
        $questionnaireToReplicate->createTranslations('name', ['nl' => $questionnaire->name]);
        $questionnaireToReplicate->is_active = false;
        $questionnaireToReplicate->save();

        // here we will replicate all the questions with the new translation, questionnaire id and question options.
        foreach ($questionnaire->questions as $question) {

            /** @var Question $questionToReplicate */
            $questionToReplicate = $question->replicate();
            $questionToReplicate->questionnaire_id = $questionnaireToReplicate->id;
            $questionToReplicate->createTranslations('name', ['nl' => $question->name]);
            $questionToReplicate->save();

            // now replicate the question options and change the question id to the replicated question.
            foreach ($question->questionOptions as $questionOption) {
                /** @var QuestionOption $questionOptionToReplicate */
                $questionOptionToReplicate = $questionOption->replicate();
                $questionOptionToReplicate->createTranslations('name', ['nl' => $questionOption->name]);
                $questionOptionToReplicate->question_id = $questionToReplicate->id;
                $questionOptionToReplicate->save();

            }
        }
    }
}
