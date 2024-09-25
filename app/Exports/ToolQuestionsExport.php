<?php

namespace App\Exports;

use App\Models\ToolQuestion;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ToolQuestionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ToolQuestion::select(['short', 'name', 'help_text'])->get();
    }

    public function headings(): array
    {
        return ['short', 'name', 'help_text'];
    }
}
