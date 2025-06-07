<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Service\Base\Components\Generator\Data\EpubCreator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Traits\Log\LoggerTrait;
use ZipArchive;

/**
 * Class TxtGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class TxtGenerator
{
    /**
     * @param array $data
     * @param string $projectDir
     * @return void
     */
    public static function generate(
        array $data,
        string $destination
    )
    {
        $fp = fopen($destination, 'w+');
        foreach ($data as $date)
        {
            fwrite($fp, $date . "\n");
        }
        fclose($fp);
    }
}
