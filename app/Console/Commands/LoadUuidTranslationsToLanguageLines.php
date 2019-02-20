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
            $this->line("Importing step: {$stepSlug}");
            $step = Step::where('slug', $stepSlug)->first();

            // start building the language line data
            foreach ($stepQuestions as $questionKey => $questions) {
                // if a question contains a title and help key,
                if (array_key_exists('title', $questions) && array_key_exists('help', $questions)) {
                    $this->line('Saving a question');
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

                        // since there is also a key called general, which contains info that repeats in all the tool pages
                        if ($step instanceof Step) {
                            $help['step_id'] = $step->id;
                            $title['step_id'] = $step->id;
                        }
                        // create the help line first, so we can use the id in the title line
                        $helpLanguageLine = LanguageLine::create($help);

                        $title['help_language_line_id'] = $helpLanguageLine->id;
                        LanguageLine::create($title);
                    }


                } else {
                    // the question has subquestions.

                    // now get the main question.
                    if (array_key_exists('title', $questions)) {
                        $this->line('Saving the main question of a subquestion');
                        $mainQuestion = $questions['title'];
                        unset($questions['title']);

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

                        // since there is also a key called general, which contains info that repeats in all the tool pages
                        if ($step instanceof Step) {
                            $mainQuestionHelp['step_id'] = $step->id;
                            $mainQuestionTitle['step_id'] = $step->id;
                        }
                        // create the help line first, so we can use the id in the title line
                        $helpLanguageLine = LanguageLine::create($mainQuestionHelp);

                        $mainQuestionTitle['help_language_line_id'] = $helpLanguageLine->id;
                        $mainQuestionLanguageLine = LanguageLine::create($mainQuestionTitle);
                    }
                    // loop through the sub questions and save those
                    foreach ($questions as $subQuestionKey => $question) {
                        $this->line('Saving sub questions');
                        if (array_key_exists('help', $question)) {
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

                            if (isset($mainQuestionLanguageLine)) {
                                $title['main_language_line_id'] = $mainQuestionLanguageLine->id;
                            }
                            // since there is also a key called general, which contains info that repeats in all the tool pages
                            if ($step instanceof Step) {
                                $help['step_id'] = $step->id;
                                $title['step_id'] = $step->id;
                            }
                            // create the help line first, so we can use the id in the title line
                            $helpLanguageLine = LanguageLine::create($help);

                            $title['help_language_line_id'] = $helpLanguageLine->id;
                            LanguageLine::create($title);

                        } else {
                            $this->line('Saving a sub-sub question');
                            // even more questions.
                            foreach ($question as $additionalKey => $additionalText) {

                                $helpTranslation = Translation::where('key', $additionalText['help'])->first();
                                $titleTranslation = Translation::where('key', $additionalText['title'])->first();

                                $helpKey = "{$questionKey}.{$subQuestionKey}.{$additionalKey}.help";
                                $titleKey = "{$questionKey}.{$subQuestionKey}.{$additionalKey}.title";

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

                                // since there is also a key called general, which contains info that repeats in all the tool pages
                                if ($step instanceof Step) {
                                    $help['step_id'] = $step->id;
                                    $title['step_id'] = $step->id;
                                }
                                if (isset($mainQuestionLanguageLine)) {
                                    $title['main_language_line_id'] = $mainQuestionLanguageLine->id;
                                }
                                // create the help line first, so we can use the id in the title line
                                $helpLanguageLine = LanguageLine::create($help);

                                $title['help_language_line_id'] = $helpLanguageLine->id;
                                LanguageLine::create($title);
                            }
                        }

                    }
                }
            }
        }
        $this->line('All translations have been added to the language line table! verry nice.', 'fg=green');
    }
}
