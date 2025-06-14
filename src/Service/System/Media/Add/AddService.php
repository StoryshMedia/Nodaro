<?php

namespace Smug\Core\Service\System\Media\Add;

use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileTypeProvider;
use Smug\Core\Service\Base\Components\Uploader\UploaderFactory;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\AddServiceInterface;
use Smug\Core\Service\Base\Service\BaseService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use \Exception;
use Smug\Core\Context\Context;

class AddService extends BaseService implements AddServiceInterface
{
    /**
     * @inheritDoc
     */
    public function add(Context $context, $import = false): array
    {
        try {
            /** @var UploaderFactory $uploaderFactory */
            $uploaderFactory = ServiceGenerationFactory::createInstance(UploaderFactory::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        if (DataHandler::doesKeyExists('data', $context->getRequestData())) {
            $data = $context->getRequestData()['data'];
        }

        if (DataHandler::isInArray($data['extension'], FileTypeProvider::IMAGE_EXTENSIONS)) {
            if (DataHandler::isStringInString($data['path'], $data['extension'])) {
                $imageSizes = $uploaderFactory->getImageSizes(
                    $_SERVER['DOCUMENT_ROOT'] . $data['path'],
                    $data['extension']
                );
            } else {
                $imageSizes = $uploaderFactory->getImageSizes(
                    $_SERVER['DOCUMENT_ROOT'] . $data['img'],
                    $data['extension']
                );
            }
	
	        $data['sizeX'] = $imageSizes['width'];
	        $data['sizeY'] = $imageSizes['height'];
        } else {
	        $data['sizeX'] = 0;
	        $data['sizeY'] = 0;
        }

        return $this->processCreateMedia($data);
    }
}
