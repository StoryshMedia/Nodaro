<?php

namespace Smug\Core\Controller\Backend\Api\System\Media;

use JMS\Serializer\SerializerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\AdministrationBundle\Trait\DispatchDataTrait;
use Smug\Core\Controller\Backend\Api\Base\BaseController;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Events\Backend\Data\DataDeletedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\PaginationHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileTypeProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\PathProvider;
use Smug\Core\Service\System\Media\Update\UpdateService;
use Smug\Core\Service\System\Media\Upload\UploadService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MediaController extends BaseController
{
    use DispatchDataTrait;

    #[Route('/be/api/media/image/upload/{folder}', name: 'file_media_image_upload')]
    #[IsGranted("ROLE_ADMIN")]
    public function uploadMediaImageAction(string $folder, UploadService $service, Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Media::class
        );

        $this->context->setConfigItem('type', $folder);
        $this->context->setConfigItem('fe', false);
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

    #[Route('/be/api/custom/media/delete', name: 'file_media_delete')]
    #[IsGranted("ROLE_ADMIN")]
    public function deleteMediaAction(
        Request $request,
        UpdateService $service
    ): JsonResponse {
        $this->context->buildFromRequest(
            $request,
            Media::class
        );

        $delete = $service->deleteMedia($this->context);
	
        $this->dispatchData(
            $this->context->getRequestData(),
            $this->context,
            DataDeletedEvent::class,
            EntityGenerator::getGeneratedEntity(Media::class),
            SystemEvents::DATA_DELETED
        );

	    return $this->prepareReturn($delete, $request);
    }

    #[Route('/be/api/media/resize', name: 'file_media_resize')]
    #[IsGranted("ROLE_ADMIN")]
    public function resizeImagesForViewportsAction(UpdateService $service, Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Media::class
        );

        $request = Request::createFromGlobals();

        $prepare = $service->resizeImagesForViewports();
	
	    return $this->prepareReturn($prepare, $request);
    } 

    #[Route('/be/api/custom/media/folder', name: 'be_get_main_media_folder', methods: ['GET'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getMainMediaFolder(): JsonResponse
    {
        $path = $this->context->getPublicDir() . DIRECTORY_SEPARATOR . '_uploads';
	
	    return $this->prepareReturn(PathProvider::getFoldersInPath($path));
    }

    #[Route('/be/api/custom/media/folder', name: 'be_get_media_folders', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getMediaFolders(Request $request): JsonResponse
    {
        $this->context->buildFromRequest($request, '');
	
	    return $this->prepareReturn(PathProvider::getFoldersInPath($this->context->getRequestData()['folder']));
    }

    #[Route('/be/api/custom/media/folder/files', name: 'be_get_media_folder_files', methods: ['POST'])]
    #[IsGranted("ROLE_ADMIN")]
    public function getMediaFolderFiles(Request $request, PaginatorInterface $pagination, SerializerInterface $serializerInterface): JsonResponse
    {
        $this->context->buildFromRequest($request, EntityGenerator::getGeneratedEntity(Media::class));
        $path = DataHandler::getReplaceString(
            $this->context->getPublicDir() . DIRECTORY_SEPARATOR,
            '',
            $this->context->getRequestData()['folder']
        );
        
        $data = $this->context->getRequestData();
        $data['ignoreHidden'] = true;
        $data['useComplete'] = true;

        if (!DataHandler::doesKeyExists('model', $data)) {
            $data['model'] = 'files';
        }

        $data['page'] = $data['page'] ?? 1;
        $data['limit'] = $data['limit'] ?? 16;

    	$queryBuilder = $this->em->createQueryBuilder();
        $query = $queryBuilder
            ->select('c')
            ->from(EntityGenerator::getGeneratedEntity(Media::class), 'c')
            ->where('c.path LIKE :path')
            ->setParameter('path', '%' . $path . '%')
            ->getQuery();

        $paginated = $pagination->paginate(
            $query,
            $data['page'],
            $data['limit'],
            ['wrap-queries'=>true]
        );

        $paginationData = $serializerInterface->serialize($paginated, 'json');

        return $this->prepareReturn(PaginationHandler::getKnpPaginatedData(DataHandler::getJsonDecode($paginationData, true), $data, $data['model']));
    }
}
