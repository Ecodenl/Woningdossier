<?php

function csv_to_array($file = '', $delimiter = ',')
{
    $header = null;
    $updatedHeader = null;
    $data = [];

    if (($handle = fopen($file, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false)
        {
            if(!$header) {
                $header = $row;
                foreach ($header as $key) {
                    $trimmedKey = strtolower(trim($key));
                    $updatedHeader[] = $trimmedKey;
                }
            } else {
                $data[] = array_combine($updatedHeader, $row);
            }


        }
        fclose($handle);
    }

    return $data;
}