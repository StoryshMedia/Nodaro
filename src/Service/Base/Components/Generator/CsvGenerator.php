<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Service\Base\Components\Generator\Data\EpubCreator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Traits\Log\LoggerTrait;
use ZipArchive;

/**
 * Class CsvGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class CsvGenerator
{
    /**
     * @param array $data
     * @param string $projectDir
     * @return void
     */
    public static function generate(
        array $data,
        array $headlines,
        string $destination
    )
    {
        $list = [$headlines];

        foreach ($data as $date) {
            if (!DataHandler::isArray($date)) {
                $date = [$date];
            }

            $list[] = $date;
        }
     
        $fp = fopen($destination, "w+");
     
        foreach ($list as $line) {
            fputcsv($fp, $line, ',');      
        }
     
        fclose($fp); 
    }
}
