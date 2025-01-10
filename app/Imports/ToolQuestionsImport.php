<?php

namespace App\Imports;

use App\Models\ToolQuestion;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ToolQuestionsImport implements ToCollection, WithCustomCsvSettings, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $short = $row['short'];
            if (! empty($short)) {
                $toolQuestionTable = (new ToolQuestion())->getTable();

                if (DB::table($toolQuestionTable)->where('short', $short)->exists()) {
                    Log::debug("updating for {$short}");
                    DB::table($toolQuestionTable)
                        ->where('short', $short)
                        ->update([
                            'name' => $row['name'],
                            'help_text' => $row['help_text'],
                        ]);
                } else {
                    Log::debug(__CLASS__."{$short} does not exist in the tool questions!");
                }
            }
        }
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ",",
            // because json.
            'escape_character' => "\""
        ];
    }
}
