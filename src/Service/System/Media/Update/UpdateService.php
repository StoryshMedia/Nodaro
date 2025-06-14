<?php

namespace Smug\Core\Service\System\Media\Update;

use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Uploader\UploaderFactory;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Service\BaseService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use \Exception;
use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Processor\RemoveProcessor;
use Smug\Core\Service\System\Media\Listing\ListService as MediaListService;
use Smug\Core\Service\System\Setting\Listing\ListService as MediaSettingListService;
use Throwable;

class UpdateService extends BaseService
{
    public function deleteMedia(Context $context): array
    {
    	$mediaRepository = $this->em->getRepository(Media::class);
    	
        $image = $mediaRepository->findOneBy(['id' => $context->getRequestData()['id']]);
        $path = $image->__get('path') . $image->__get('file') . '.' . $image->__get('extension');

        try {
            DataHandler::deleteFile($context->getPublicDir() . DIRECTORY_SEPARATOR . $path);
        } catch (Throwable $e) {
            
        }

        RemoveProcessor::process($this->em, $this->setter, null, '', [], false, $image);

        return [
            'success' => true
        ];
    }

    /**
     * @return array
     */
    public function resizeImagesForViewports(): array
    {
        try {
            /** @var MediaListService $listService */
            $listService = ServiceGenerationFactory::createInstance(MediaListService::class);
            /** @var MediaSettingListService $listSettingService */
            $listSettingService = ServiceGenerationFactory::createInstance(MediaSettingListService::class);
            /** @var UploaderFactory $uploaderFactory */
	        $uploaderFactory = ServiceGenerationFactory::createInstance(UploaderFactory::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        $settings = $listSettingService->getMediaSettings();

        $mediaCategories = $listService->getData();

        try {
            foreach ($mediaCategories as $category) {
                foreach ($category['media'] as $media) {
                    $imageSrc = $uploaderFactory->getMediaPath($media['path']);

                    if (!DataHandler::doesFileExist($imageSrc)) {
                        continue;
                    }

                    /** @var resource $imageObj */
                    $imageResource = $uploaderFactory->getImageObjectFromPath($imageSrc);
                    /** @var array $imageSizes */
                    $imageSizes = $uploaderFactory->getImageSizes($imageResource, $media['extension']);

                    foreach ($settings as $setting) {
                        $newImageDestination = $uploaderFactory->getNewMediaPath($media, $setting);

                        if ($setting['imageSize'] < $imageSizes['width']) {
                            $ratio = $setting['imageSize'] / $imageSizes['width'];

                            $newSize = [
                                'width' => $imageSizes['width'] * $ratio,
                                'height' => $imageSizes['height'] * $ratio
                            ];
                        } else {
                            $newSize = [
                                'width' => $imageSizes['width'],
                                'height' => $imageSizes['height']
                            ];
                        }

                        $newImage = $uploaderFactory->createNewImage(
                            $imageResource,
                            $imageSizes,
                            $newSize,
                            $media['extension']
                        );

                        $uploaderFactory->writeNewImage(
                            $newImageDestination,
                            $newImage,
                            $setting['quality'],
                            $media['extension']
                        );

                        $this->setThumbnailToMedia([
                            'path' => $newImageDestination,
                            'media' => $media,
                            'sizes' => $newSize,
                            'variant' => $setting['name']
                        ]);

                        imagedestroy($newImage);
                    }
                    imagedestroy($imageResource);
                }
            }
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true,
            'message' => 'all images are prepared'
        ];
    }
}
