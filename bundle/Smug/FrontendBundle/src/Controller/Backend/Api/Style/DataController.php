<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Style;

use Smug\Core\Context\Context;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends FeBaseController
{
    #[Route('/be/api/custom/site/styles/{id}', name: 'be_get_site_styles', methods:"GET")]
    public function getSiteScripts(Request $request, string $id): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Site::class
        );

        $data = $this->context->getEntityByIdentifier($id);

        if (DataHandler::isEmpty($data)) {
            return $this->prepareReturn(
                [
                    'success' => false
                ]
            );    
        }

        return $this->prepareReturn(
            [
                'success' => true,
                'data' => $data->__get('siteStyles')
            ]
        );
    }

    #[Route('/be/api/custom/frontend/styles', name: 'be_get_available_style_steets', methods:"GET")]
    public function getAvailableStyleSheets(Context $context): JsonResponse
    {
        $assets = [
        ];

        $finder = new Finder();
        $finder->files()->in($context->getProjectDir() . "/bundle")->name(['*.scss']);

        foreach ($finder as $file) {
            if ($file === '.' || $file === '..' ||  !stristr($file->getFilename(), 'scss')) {
                continue;
            }
            if (!DataHandler::isStringInString($file->getRelativePath(), 'styles')) {
                continue;
            }

            $assets[] = [
                'title' => self::getStyleBundle($file->getRelativePath()) . ': ' . DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-'),
                'value' => self::getStylePrefix($file->getRelativePath()) . '-' . DataHandler::getCamelCaseString($file->getFileNameWithoutExtension(), '-')
            ];
        }

        return $this->prepareReturn(
            [
                'success' => true,
                'data' => $assets
            ]
        );
    }

    #[Route('/be/api/custom/site/script/reorder', name: 'be_reorder_site_scripts', methods:"PUT")]
    public function reorderSiteScripts(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            SiteScript::class
        );
        $count = 0;

        foreach ($this->context->getRequestData() as $siteScript) {
            $siteScriptObject = $this->context->getEntityByIdentifier($siteScript['id']);
            $siteScriptObject->__set('position', $count);

            $this->context->getEntityManager()->persist($siteScriptObject);
            $this->context->getEntityManager()->flush();

            $count++;
        }

        return $this->prepareReturn(['success' => true]);
    }

    private static function getStylePrefix(string $path): string
    {
        $pathArray = DataHandler::explodeArray('/', $path);
        
        return DataHandler::getLowerString($pathArray[0]) . '-' . DataHandler::getLowerString($pathArray[1]);
    }

    private static function getStyleBundle(string $path): string
    {
        $pathArray = DataHandler::explodeArray('/', $path);
        
        return $pathArray[1];
    }
}