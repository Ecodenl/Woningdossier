<?php

namespace App\Console\Commands;

use App\Models\Step;
use App\Models\Translation;
use App\Models\LanguageLine;
use Illuminate\Console\Command;

class LoadUuidTranslationsToLanguageLines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:uuid-translatable-to-language-lines-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the translations from the uuid.php and translations table to the language_lines table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function createQuestion($step, $titleData, $helpData)
    {

//        $title['main_language_line_id'] = $mainQuestionLanguageLine->id;
        // since there is also a key called general, which contains info that repeats in all the tool pages

        if ($step instanceof Step) {
            $helpData['step_id'] = $step->id;
            $titleData['step_id'] = $step->id;
        }
        // create the help line first, so we can use the id in the title line
        $helpLanguageLine = LanguageLine::create($helpData);

        $titleData['help_language_line_id'] = $helpLanguageLine->id;
        return LanguageLine::create($titleData);
    }

    /**
     * If a given questions array contains a title key, it has sub questions.
     *
     * @param array $questions
     * @return bool
     */
    public function hasSubQuestions(array $questions)
    {
        if (array_key_exists('title', $questions)) {
            return true;
        }
        return false;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $uuid = __('uuid');
        /**
         * Function to migrate the data from the uuid.php to the laravel translation loader from spatie.
         */
        foreach ($uuid as $stepSlug => $stepQuestions) {
            if ($stepSlug == 'boiler') {
                $stepSlug = 'high-efficiency-boiler';
            }
            $step = Step::where('slug', $stepSlug)->first();

            // start building the language line data
            foreach ($stepQuestions as $questionKey => $questions) {
                // if a question contains a title and help key,
                if (array_key_exists('title', $questions) && array_key_exists('help', $questions)) {

                    $question = $questions;
                    $helpTranslation = Translation::where('key', $question['help'])->first();
                    $titleTranslation = Translation::where('key', $question['title'])->first();

                    if ($titleTranslation instanceof Translation) {

                        // build base array
                        $help = [
                            'group' => $stepSlug,
                            'key' => $questionKey . '.help',
                            'text' => ['nl' => $helpTranslation->translation],
                        ];
                        $title = [
                            'group' => $stepSlug,
                            'key' => $questionKey . '.title',
                            'text' => ['nl' => $titleTranslation->translation],
                        ];
                        $this->createQuestion($step, $title, $help);
                    }


                } else {
                    if ($this->hasSubQuestions($questions)) {

                        $mainQuestion = $questions['title'];

                        $helpTranslation = Translation::where('key', $mainQuestion['help'])->first();
                        $titleTranslation = Translation::where('key', $mainQuestion['title'])->first();

                        $helpKey = "{$questionKey}.title.help";
                        $titleKey = "{$questionKey}.title.title";

                        // build base array
                        $mainQuestionHelp = [
                            'group' => $stepSlug,
                            'key' => $helpKey,
                            'text' => ['nl' => $helpTranslation->translation],
                        ];
                        $mainQuestionTitle = [
                            'group' => $stepSlug,
                            'key' => $titleKey,
                            'text' => ['nl' => $titleTranslation->translation],
                        ];
                        $main = $this->createQuestion($step, $mainQuestionTitle, $mainQuestionHelp);
                    }

                    // loop through the sub questions and save those
                    foreach ($questions as $subQuestionKey => $question) {
                        if (array_key_exists('help', $question) && $subQuestionKey != 'title') {
                            $helpTranslation = Translation::where('key', $question['help'])->first();
                            $titleTranslation = Translation::where('key', $question['title'])->first();

                            $helpKey = "{$questionKey}.{$subQuestionKey}.help";
                            $titleKey = "{$questionKey}.{$subQuestionKey}.title";

                            // build base array
                            $help = [
                                'group' => $stepSlug,
                                'key' => $helpKey,
                                'text' => ['nl' => $helpTranslation->translation],
                            ];
                            $title = [
                                'group' => $stepSlug,
                                'key' => $titleKey,
                                'text' => ['nl' => $titleTranslation->translation],

                            ];
                            if (isset($main)) {
                                $title['main_language_line_id'] = $main->id;
                            }
                            $this->createQuestion($step, $title, $help);

                        }
                    }
                    unset($main);
                }
            }
        }
    }
}
