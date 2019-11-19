<?php

namespace App\Console\Commands;

use App\Models\LanguageLine;
use function Couchbase\defaultDecoder;
use Illuminate\Console\Command;

class LanguageLineToQuestionStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'languageline:to-question-structure {--groups-to-convert= : pass the groups to convert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts regular language lines to a question structure, will set help_language_line_id';

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

        // temporary command to convert normal translations to question like structure
        // this will be removed in the near future as refactor is needed
        $groupsToConvert = explode(',', $this->option('groups-to-convert'));

        foreach ($groupsToConvert as $groupToConvert) {
            // get the translations to convert.
            $translations = LanguageLine::forGroup($groupToConvert)->get();

            $this->alert("Converting the group {$groupToConvert}");
            foreach ($translations as $translation) {
                // determine if its a title
                $translationKeys = explode('.', $translation->key);
                if (in_array('title', $translationKeys)) {
                    $this->line("The translation with id: {$translation->id} is a title");
                    // now we have to find the corresponding helptext
                    // pop of the title key
                    array_pop($translationKeys);
                    // push the help key
                    array_push($translationKeys, 'help');
                    // implode it ans search the key in the current translations groups
                    $helpTextForTranslation = $translations->where('key', implode('.', $translationKeys))->first();

                    // sometimes there is a title without a help text.
                    if ($helpTextForTranslation instanceof LanguageLine) {
                        $this->line("Helptext found! id: {$helpTextForTranslation->id}");
                        $translation->update(['help_language_line_id' => $helpTextForTranslation->id]);
                    }
                }
            }
        }

    }
}
