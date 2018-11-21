<?php

namespace App\Services;

class CsvExportService
{
    public static function export($headers, $contents, $filename = 'export')
    {
        // set the headers for the browser
        $filename = str_replace('.csv', '', $filename);
        $browserHeaders = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename='.$filename.'.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        // write the CSV file
        $callback = function () use ($headers, $contents) {

            $file = fopen('php://output', 'w');
            fputcsv($file, $headers, ';');

            foreach ($contents as $contentRow) {
                fputcsv($file, $contentRow, ';');
            }

            fclose($file);
        };

        return \Response::stream($callback, 200, $browserHeaders);
    }
}
