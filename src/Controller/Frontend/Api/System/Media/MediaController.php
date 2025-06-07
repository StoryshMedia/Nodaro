<?php

namespace Smug\Core\Controller\Frontend\Api\System\Media;

use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileTypeProvider;
use Smug\Core\Service\System\Media\Upload\UploadService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends BaseController
{
    #[Route('/fe/api/media/image/upload/{folder}', name: 'fe_file_media_image_upload')]
    public function uploadMediaImageAction(string $folder, UploadService $service, Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Media::class
        );

        $this->context->setConfigItem('type', $folder);
        $this->context->setConfigItem('fe', true);
        $this->context->setConfigItem('allowedExtensions', FileTypeProvider::IMAGE_EXTENSIONS);
        $this->context->setConfigItem('albumString', 'web');
        
        $request = Request::createFromGlobals();
        $files = $request->files->get('files');

        foreach ($files as $file) {
            $this->context->setRequestData(['file' => $file]);

            $upload[] = $service::upload($this->context);
        }
	
	    return $this->prepareReturn($upload, $request);
    }
}
