<?php

namespace Smug\Core\Service\Base\Components\Uploader;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\Document;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\DocumentDataProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;
use Smug\Core\Service\Base\Components\Writer\FileWriter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    public function upload(UploadedFile $file): array
    {
        $document = new Document();

        $orgName = $file->getClientOriginalName();
        $name_array = DataHandler::explodeArray('.', $orgName);
        $extension = $name_array[DataHandler::getSizeOf($name_array) - 1];

        $document->setFile($file);
        $document->setSubDirectory(DocumentDataProvider::DEFAULT_ATTACHMENT_PATH);

        $hashName = DataHandler::getMd5Hash(
            $orgName . TimeHandler::getDate(TimeProvider::DATE_OUTPUT_FORMAT)
        );
        $hashName = $hashName . '.' . $extension;

        $file->move($document->getSubDirectory(), $hashName);

        $name_array = DataHandler::explodeArray('.', $orgName);
        $extension = DataHandler::getLowerString(
            $name_array[DataHandler::getSizeOf($name_array) - 1]
        );

        return [
            'hashName' => $hashName,
            'file' => [
                'extension' => $extension,
                'path' => '/' . DocumentDataProvider::DEFAULT_ATTACHMENT_PATH . '/' . $hashName,
            ],
            'orgName' => $orgName
        ];
    }

    public function uploadFile(Context $context)
    {
        $image = $context->getRequestData()['file'];
        
        $isValid = UploaderFactory::validateUpload($context, $image);

        if ($isValid['success'] === false) {
            return $isValid;
        }

        $orgName = $context->getConfigItem('orgName');

        $directory = (!DataHandler::doesKeyExists($context->getConfigItem('type'), DocumentDataProvider::MEDIA_DIRECTORIES)) ? '_uploads/images/media/' . $context->getConfigItem('type') : DocumentDataProvider::MEDIA_DIRECTORIES[$context->getConfigItem('type')];
        $data = [
            'subDirectory' => $directory,
            'orgName' => $orgName
        ];

        $src = FileWriter::createFileOnFileSystem($image, $data);

        return [
            'size' => DataHandler::getFileSize($src),
            'extension' => UploaderFactory::getFileExtension($orgName),
            'orgName' => $orgName,
            'file' => DataHandler::getFileNameWithoutExtension($orgName),
            'img' => PathProvider::getHost() . '/' . $src,
            'data' => $data,
            'path' => $data['subDirectory'] . '/'
        ];
    }
}
