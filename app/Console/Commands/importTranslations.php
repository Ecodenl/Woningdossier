<?php

namespace App\Console\Commands;

use App\Helpers\Str;
use App\Models\Translation;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class importTranslations extends Command
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

        // the update array for the db
        $updateData = [];
        // to create a new csv with updated data
        $csvData = [];

        // the uuids
        $translationUuidHelpKey = "";
        $translationUuidTitleKey = "";

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
                foreach ($csvRow as $key => $row) {

                    // if the uuid key does not exist or the uuid is empty create a new one
                    if ((!array_key_exists('help-uuid', $csvRow) || $csvRow['help-uuid'] == "") || (!array_key_exists('title-uuid', $csvRow) || $csvRow['title-uuid'] == "")) {
                        $translationUuidHelpKey = Str::uuid();
                        $translationUuidTitleKey = Str::uuid();
                        $this->line('The title: '.$csvRow[0]. 'does not have a uuid');
                    } else {
                        $translationUuidHelpKey = $csvRow[4];
                        $translationUuidTitleKey = $csvRow[5];
                    }

                    $updateData[$csvRow[1]] = [
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
                }

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


        foreach ($updateData as $translations) {
            foreach ($translations as $translationKey => $translationArray) {
                $translation = Translation::updateOrCreate(
                    [
                        'translation' => $translationArray['translation']
                    ],
                    $translationArray
                );

                if ($translation->wasChanged()) {
                    $this->line('The translation with id: '. $translation->id. ' has been updated');
                } elseif ($translation->wasRecentlyCreated) {
                    $this->line('The translation with id: '. $translation->id. ' is nieuw aangemaakt');
                } else {
                    $this->line('Nothing has been updated or created.');
                }
            }
        }

        // dot the array with the key as dot array and value as the translation uuid key
        $translationDottedFileArray = [];
        foreach ($updateData as $translations) {
            foreach ($translations as $translationKey => $translation) {
                array_push($translationDottedFileArray, [$translationKey => $translation['key']]);
            }
        }

        // undot the array and create a "normal" php array
        $translationFileArray = [];
        foreach ($translationDottedFileArray as $values) {
            foreach ($values as $key => $value) {
                array_set($translationFileArray, $key, "'".$value."',");
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
        $this->line('Done!', "fg=green");
    }
}
