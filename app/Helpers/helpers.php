<?php

/**
 * Convert a csv to a associative array with the header / first row of the csv as array keys.
 * when $csvHeaderAsArrayKeys is set to false it will use normal array indexes
 *
 * @param string $file
 * @param string $delimiter
 * @param bool $csvHeaderAsArrayKeys
 * @return array
 */
function csv_to_array($file = '', $delimiter = ',', $csvHeaderAsArrayKeys = true): array
{
    $header = null;
    $updatedHeader = null;
    $data = [];

    if (($handle = fopen($file, 'r')) !== false)
    {
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false)
        {
            if ($csvHeaderAsArrayKeys) {
                if(!$header) {
                    $header = $row;
                    foreach ($header as $key) {
                        $trimmedKey = strtolower(trim($key));
                        $updatedHeader[] = $trimmedKey;
                    }
                } else {
                    $data[] = array_combine($updatedHeader, $row);
                }
            } else {
                // we will still set the header, otherwise the header would be in the $data
                if ($header == null) {
                    $header = $row;
                } else {
                    $data[] = $row;
                }

            }


        }
        fclose($handle);
    }

    return $data;
}