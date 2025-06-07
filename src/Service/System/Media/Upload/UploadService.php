<?php

namespace Smug\Core\Service\System\Media\Upload;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Generator\ThumbnailGenerator;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileTypeProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\MediaProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\Base\Components\Uploader\Uploader;
use Smug\Core\Service\Base\Components\Uploader\UploaderFactory;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Service\BaseService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\System\Media\Add\AddService as MediaAddService;
use Smug\Core\Service\System\Media\Listing\ListService as MediaListService;
use Intervention\Image\ImageManagerStatic as Image;
use \Exception;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;

class UploadService extends BaseService
{
    public static function upload(Context $context): array
    {
        try {
            /** @var Uploader $uploader */
            $uploader = ServiceGenerationFactory::createInstance(Uploader::class);
            /** @var MediaAddService $addService */
            $addService = ServiceGenerationFactory::createInstance(MediaAddService::class);
            /** @var MediaListService $listService */
            $listService = ServiceGenerationFactory::createInstance(MediaListService::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        $imageName = DataHandler::getReplaceString(' ', '_', $context->getRequestData()['file']->getClientOriginalName());
        $imgData = $listService->proofExistingFile(
            DataHandler::getFileParts($imageName)
        );

        $context->setConfigItem(
            'orgName',
            (!DataHandler::isEmpty($imgData)) ? $imgData->__get('file') . '_' . DataHandler::getUniqueId() . '.' . $imgData->__get('extension') : $imageName
        );

        $image = $uploader->uploadFile($context);

        if (isset($image['success'])) {
            return $image;
        }

        $class = EntityGenerator::getGeneratedEntity(Media::class);
        /** @var Context $addContext */
        $addContext = ServiceGenerationFactory::createInstance(Context::class);
        $addContext->buildFromData(
            [
                'data' => $image,
                'albumString' => $context->getConfigItem('albumString')
            ],
            $class
        );

        $image = $addService->add($addContext);

        /** @var BaseModel $imageObj */
        $imageObj = $context->getEntityManager()->getRepository($class)->findOneBy(['id' => $image['id']]);

        if (DataHandler::isInArray($imageObj->__get('extension'), FileTypeProvider::IMAGE_EXTENSIONS) && $imageObj->__get('extension') !== 'webp') {
            try {
                $extension = $imageObj->__get('extension');
                $fileName = $imageObj->__get('file');
                Image::configure(['driver' => 'imagick']);
                $imageSrc = $context->getProjectDir() . '/public/' . PathProvider::getPathWithoutLeadingBackSlash($imageObj->__get('path'));
                $imageSrc = $imageSrc . '/' . $fileName . '.' . $extension;
    
                $imageResource = Image::make($imageSrc);
                $newDestination = DataHandler::getReplaceString($extension, 'webp', $imageSrc);

                $imageResource->encode('webp', 90)->save($newDestination, 90);

                $imageObj->__set('extension', 'webp');
                $context->getEntityManager()->persist($imageObj);
                $context->getEntityManager()->flush();
            } catch (\Exception $e) {
            }
        }

        if (DataHandler::isInArray($imageObj->__get('extension'), FileTypeProvider::IMAGE_EXTENSIONS)) {
            self::generateThumbnails($imageObj, $context);
        }

        return ['media' => $imageObj->toArray()];
    }

    /**
     * @param BaseModel $media
     */
    private static function generateThumbnails(BaseModel $media, Context $context)
    {
        try {
            /** @var UploaderFactory $uploaderFactory */
            $uploaderFactory = ServiceGenerationFactory::createInstance(UploaderFactory::class);
        } catch (Exception $exception) {
        }

        foreach (MediaProvider::getThumbnails(($media->__get('sizeX') > $media->__get('sizeY'))) as $thumbnailKey => $thumbnail) {
            ThumbnailGenerator::generate(
                $thumbnailKey,
                $thumbnail,
                $media,
                $uploaderFactory,
                $context->getEntityManager(),
                $context->getProjectDir()
            );
        }
    }
}
