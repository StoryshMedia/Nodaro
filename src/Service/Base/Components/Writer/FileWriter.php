<?php

namespace Smug\Core\Service\Base\Components\Writer;

use Smug\Core\Entity\Base\Document;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileWriter
{
    const BASE_DIRECTORY = '/../../../';
    
    public static function createFileOnFileSystem(UploadedFile $image, array $data): string
    {
        $document = new Document();

        $name = DataHandler::getReplaceString(' ', '_', $data['orgName']);

        $document->setFile($image);
        $document->setSubDirectory($data['subDirectory']);

        $image->move($document->getSubDirectory(), $name);

        return $data['subDirectory'] . '/' . $name;
    }

    public static function writeFile(string $template, string $path): bool
    {
        $file = __DIR__ . self::BASE_DIRECTORY . $path;

        $fp = fopen($file, 'w+');
        fputs($fp, $template);
        fclose($fp);

        return true;
    }
}
