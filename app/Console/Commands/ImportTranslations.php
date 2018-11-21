<?php

namespace App\Console\Commands;

use App\Helpers\Str;
use App\Models\Translation;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class ImportTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:translations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import the translations from the translations import csv file located under storage/app/public/';

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
        $importFile = storage_path('app/public/translations-import.csv');

        // csv to associative array
        $csvRows = csv_to_array($importFile, ',', false);

        // update for the translatable uuid.php file
        $uuidTranslatableData = [];
        // to create a new csv with updated data
        $csvData = [];

        $updateHelpTranslations = [];
        $updateTitleTranslations = [];

        // fill the update array
        /*
         * Array key indexes
         *
         * 0 = tabblad
         * 1 = veldnaam
         * 2 = uitleg
         * 3 = short
         * 4 = help-uuid
         * 5 = title-uuid
         */
        foreach ($csvRows as $csvKey => $csvRow) {

            // check if the short is empty
            if ($csvRow[3] != "") {
                $short = $csvRow[3];

                // if the uuid key does not exist or the uuid is empty create a new one
                if (array_key_exists(4, $csvRow) && $csvRow != "") {
                    $translationUuidHelpKey = $csvRow[4];
                } else {
                    $translationUuidHelpKey = Str::uuid();
                }
                if (array_key_exists(5, $csvRow) && $csvRow[5] != "") {
                    $translationUuidTitleKey = $csvRow[5];
                } else {
                    $translationUuidTitleKey = Str::uuid();
                }

                // update for the help translations
                $updateHelpTranslations[] = [
                    'key' => $translationUuidHelpKey,
                    'language' => 'nl',
                    'translation' => $csvRow[2]
                ];

                // update for the title translations
                $updateTitleTranslations[] = [
                    'language' => 'nl',
                    'key' => $translationUuidTitleKey,
                    'translation' => $csvRow[1]
                ];

                // update for the translatable uuid.php file
                $uuidTranslatableData[] = [
                    $short . ".help" => [
                        'key' => $translationUuidHelpKey,
                        'language' => 'nl',
                        'translation' => $csvRow[2]
                    ],
                    $short . ".title" => [
                        'language' => 'nl',
                        'key' => $translationUuidTitleKey,
                        'translation' => $csvRow[1]
                    ]
                ];


                $csvData[] = [
                    $csvRow[0],
                    $csvRow[1],
                    $csvRow[2],
                    $csvRow[3],
                    $translationUuidHelpKey,
                    $translationUuidTitleKey,
                ];
            }
        }

        // Column names
        $headers = [
            'tabblad',
            'veldnaam',
            'uitleg',
            'short',
            'help-uuid',
            'title-uuid'
        ];

        $contents = $csvData;
        $file = fopen(storage_path('app/public/translations-import.csv'), 'w');

        // write the CSV file
        fputcsv($file, $headers, ',');

        foreach ($contents as $contentRow) {
            fputcsv($file, $contentRow, ',');
        }

        fclose($file);

        $updateCounter = 0;
        $createCounter = 0;

        foreach ($updateHelpTranslations as $updateHelpTranslation) {
            $helpTranslation = Translation::where('key', $updateHelpTranslation['key'])->first();

            // check if the translation exists.
            // if so, we can see if it needs a update
            // else create a new one.
            if ($helpTranslation instanceof Translation) {
                // check if the translation from the csv differs from the translation in the database
                // ifso update it
                if ($updateHelpTranslation['translation'] != $helpTranslation->translation) {
                    $updateCounter++;
                    Translation::where('key', $updateHelpTranslation['key'])->update([
                        'key' => $updateHelpTranslation['key'],
                        'language' => $updateHelpTranslation['language'],
                        'translation' => $updateHelpTranslation['translation']
                    ]);
                }

            } else {
                Translation::create([
                    'key' => $updateHelpTranslation['key'],
                    'language' => $updateHelpTranslation['language'],
                    'translation' => $updateHelpTranslation['translation']
                ]);
                $createCounter++;
            }
        }
        foreach ($updateTitleTranslations as $updateTitleTranslation) {
            $titleTranslation = Translation::where('key', $updateTitleTranslation['key'])->first();

            // check if the translation exists.
            // if so, we can see if it needs a update
            // else create a new one.
            if ($titleTranslation instanceof Translation) {
                // check if the translation from the csv differs from the translation in the database
                // ifso update it
                if ($updateTitleTranslation['translation'] != $titleTranslation->translation) {
                    $updateCounter++;
                    Translation::where('key', $updateTitleTranslation['key'])->update([
                        'key' => $updateTitleTranslation['key'],
                        'language' => $updateTitleTranslation['language'],
                        'translation' => $updateTitleTranslation['translation']
                    ]);
                }

            } else {
                Translation::create([
                    'key' => $updateTitleTranslation['key'],
                    'language' => $updateTitleTranslation['language'],
                    'translation' => $updateTitleTranslation['translation']
                ]);
                $createCounter++;
            }
        }

        $this->line("Created counter: {$createCounter}");
        $this->line("Update counter: {$updateCounter}");




        // dot the array with the key as dot array and value as the translation uuid key
        $translationDottedFileArray = [];
        foreach ($uuidTranslatableData as $translations) {
            foreach ($translations as $translationKey => $translation) {
                array_push($translationDottedFileArray, [$translationKey => $translation['key']]);
            }
        }

        // undot the array and create a "normal" php array
        $translationFileArray = [];
        foreach ($translationDottedFileArray as $values) {
            foreach ($values as $key => $value) {
                array_set($translationFileArray, trim($key), "'".$value."',");
            }
        }

        // filepath of the uuid translation file
        $this->line('Writing the uuid translatable file...', "fg=blue");
        $translationPath = resource_path('lang/nl/uuid.php');
        $translationFile = fopen($translationPath, 'w');

        // array to string
        $translationFileContent =  "<?php return ". print_r($translationFileArray, true);
        // strip, replace and lower the necessary things to get valid php
        $translationFileContent =  str_replace(']', '"', $translationFileContent);
        $translationFileContent =  str_replace('[', '"', $translationFileContent);
        $translationFileContent =  strtolower($translationFileContent);

        $translationFileContent = str_replace('array', '', $translationFileContent);
        $translationFileContent = str_replace('(', '[', $translationFileContent);
        $translationFileContent = str_replace(')', '],', $translationFileContent);
        $translationFileContent = str_replace_last(',', ';', $translationFileContent);

        // write and close the file
        fwrite($translationFile, $translationFileContent);
        fclose($translationFile);

        $this->line($createCounter." translations have been created and ".$updateCounter." have been updated.");
        $this->line('Done!', "fg=green");

    }
}
