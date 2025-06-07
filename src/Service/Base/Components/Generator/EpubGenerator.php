<?php

namespace Smug\Core\Service\Base\Components\Generator;

use Smug\Core\Service\Base\Components\Generator\Data\EpubCreator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Traits\Log\LoggerTrait;
use ZipArchive;

/**
 * Class EpubGenerator
 * @package Smug\Core\Service\Base\Components\Generator
 */
class EpubGenerator
{
    /**
     * @param array $data
     * @param string $projectDir
     * @return void
     */
    public static function generate(
        array $data,
        string $projectDir
    )
    {
        $title = DataHandler::getReplaceString(' ', '', $data['title']);
        $title .= '_' . $data['author']['name'];

        $epub = new EpubCreator();
        $epub->temp_folder = $projectDir . '/public/epubTemp';
        $epub->epub_file = $projectDir . '/public/downloads/stories/' . $title . '.epub';

        $epub->title = $data['title'];

        foreach ($data['chapters'] as $chapter) {
            $epub->AddPage($chapter['text'], false, $chapter['title']);
        }

        $counter = 0;
        foreach ($data['images'] as $image) {
            $epub->AddImage( $image['src'], 'image/' . $image['extension'], ($counter === 0) );
            $counter++;
        }

        if (!$epub->error) {
            $epub->CreateEPUB();

            if ($epub->error) {
                LoggerTrait::logInfo($epub->error, []);
            } else {
                $filename = $projectDir . '/public/downloads/stories/' . $title . '-1.zip';
                $archive = new ZipArchive();

                if ($archive->open($filename, ZipArchive::CREATE)!==TRUE) {
                    LoggerTrait::logInfo("cannot open <$filename>\n", []);
                }
                $file = $projectDir . '/public/downloads/stories/' . $title . '.epub';               
                $localName = basename($file);

                $archive->addFile($file, $localName);
                $archive->close();

                DataHandler::deleteFile($projectDir . '/public/downloads/stories/' . $title . '.epub');
            }
        } else {
            LoggerTrait::logInfo($epub->error, []);
        }
    }
}
