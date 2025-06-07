<?php

namespace Smug\Core\Service\Base\Components\File;

class CsvToArray
{
    /**
     * @param string $path
     * @param string $separator
     * @return array
     */
    public static function convert(string $path, string $separator = ';'): array
    {
        $file = fopen($path, 'r');
        $header = [];
        $result = [];
        $count = 0;
        while (($line = fgetcsv($file, 0, $separator)) !== FALSE) {
            //$line is an array of the csv elements
            if ($count === 0) {
                $header = $line;
            } else {
                $subResult = [];
                foreach ($header as $headerKey => $headerItem) {
                    $subResult[$headerItem] = $line[$headerKey];
                }
                $result[] = $subResult;
            }

            $count++;
        }
        fclose($file);

        return $result;
    }
}