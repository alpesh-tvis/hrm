<?php 
namespace App\Helpers;

class CsvHelper
{
    public static function parse($file)
    {
        $data = [];

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {

            $headers = fgetcsv($handle, 1000, ',');

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($headers) == count($row)) {
                    $data[] = array_combine($headers, $row);
                }
            }

            fclose($handle);
        }

        return $data;
    }
}