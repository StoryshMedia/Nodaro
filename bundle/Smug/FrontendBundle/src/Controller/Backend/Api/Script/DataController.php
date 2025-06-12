<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Script;

use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Factory\Finder\FinderFactory;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\FrontendBundle\Entity\Script\Script;
use Smug\FrontendBundle\Entity\ScriptField\ScriptField;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Smug\FrontendBundle\Service\Script\Add\AddService;
use Symfony\Component\HttpFoundation\JsonResponse;

class DataController extends FeBaseController
{
    #[Route('/be/api/custom/script/scan', name: 'be_script_scan_for_modules', methods:"POST")]
    public function scan(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Script::class
        );

        foreach (
            DataHandler::mergeArray(
                FinderFactory::getElements($this->context->getKernel()->getProjectDir() . "/bundle/", 0, false),
                FinderFactory::getElements($this->context->getKernel()->getProjectDir() . "/custom/", 0, false)
            )
            as $file
        ) {
            foreach (
                FinderFactory::getElements($file->getRealPath(), 0, false)
                as $bundle
            ) {
                foreach (
                    FinderFactory::getElements($bundle->getRealPath() . DIRECTORY_SEPARATOR . 'config/frontend/script', -1, true, ['*.json'])
                    as $script
                ) {
                    $scriptData = DataHandler::getJsonDecode(
                        DataHandler::getFile($script->getRealPath()),
                        true
                    );

                    $exsistingScript = $this->context->getEntityByIdentifier($scriptData['identifier'], 'identifier');

                    if (!DataHandler::isEmpty($exsistingScript)) {
                        continue;
                    }

                    $newScript = new Script();
                    $newScript->__set('title', $scriptData['title']);
                    $newScript->__set('configFile', $script->getRealPath());
                    $newScript->__set('identifier', $scriptData['identifier']);
                    $newScript->__set('template', DataHandler::getJsonEncode($scriptData['settings']['template'] ?? []));
                    $newScript->__set('externalSrc', $scriptData['settings']['externalSrc'] ?? '');
                    $newScript->__set('plainScript', $scriptData['settings']['plainScript'] ?? '');
                    $newScript->__set('installed', false);
                    $newScript->__set('active', false);
                    $newScript->__set('description', $scriptData['description']['de']);
                    
                    $this->context->getEntityManager()->persist($newScript);
                    $this->context->getEntityManager()->flush($newScript);
                }
            }
        }

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/empty/install', name: 'be_script_install_empty', methods:"POST")]
    public function installEmpty(): JsonResponse
    {        
        $newScript = new Script();
        $newScript->__set('title', 'Untitled Script');
        $newScript->__set('configFile', '');
        $newScript->__set('identifier', DataHandler::getUniqueId());
        $newScript->__set('template', '');
        $newScript->__set('externalSrc',  '');
        $newScript->__set('plainScript', '');
        $newScript->__set('installed', false);
        $newScript->__set('active', false);
        $newScript->__set('description', '');

        $this->context->getEntityManager()->persist($newScript);
        $this->context->getEntityManager()->flush($newScript);

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/script/install', name: 'be_script_install', methods:"PUT")]
    public function install(Request $request, AddService $addService): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Script::class
        );

        $script = $this->context->getMainEntity();
        $script->__set('installed', true);

        $this->context->getEntityManager()->persist($script);
        $this->context->getEntityManager()->flush();

        $configFilePath = $script->__get('configFile');
        
        if (!DataHandler::isEmpty($configFilePath)) {
            $scriptData = DataHandler::getJsonDecode(
                DataHandler::getFile($configFilePath),
                true
            );
    
            $this->context->addRepository('field', ScriptField::class);
    
            if (DataHandler::doesKeyExists('plugin', $scriptData['settings'])) {
                $addService->installScriptFields($scriptData['settings']['plugin']['fields'] ?? [], $this->context, $script);
            }
        }

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/script/update', name: 'be_script_update', methods:"PUT")]
    public function update(Request $request, AddService $addService): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Script::class
        );

        $script = $this->context->getMainEntity();

        $configFilePath = $script->__get('configFile');
        
        $scriptData = DataHandler::getJsonDecode(
            DataHandler::getFile($configFilePath),
            true
        );

        $script->__set('title', $scriptData['title']);
        $script->__set('category', $scriptData['category']);
        $script->__set('type', $scriptData['type'] ?? 'contentElement');
        $script->__set('multi', $scriptData['multi'] ?? false);
        $script->__set('description', $scriptData['description']['de']);
        $script->__set('template', DataHandler::getJsonEncode($scriptData['settings']['template'] ?? []));
        $script->__set('scripts', DataHandler::getJsonEncode($scriptData['settings']['scripts'] ?? []));
        
        $this->context->getEntityManager()->persist($script);
        $this->context->getEntityManager()->flush($script);

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/script/deinstall', name: 'be_script_deinstall', methods:"PUT")]
    public function deinstall(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $script = $this->context->getMainEntity();
        $script->__set('installed', false);
        $script->__set('active', false);

        $this->context->getEntityManager()->persist($script);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/script/activate', name: 'be_script_activate', methods:"PUT")]
    public function activate(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Script::class
        );

        $script = $this->context->getMainEntity();
        $script->__set('active', true);

        $this->context->getEntityManager()->persist($script);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/script/deactivate', name: 'be_script_deactivate', methods:"PUT")]
    public function deactivate(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Script::class
        );

        $script = $this->context->getMainEntity();
        $script->__set('active', false);

        $this->context->getEntityManager()->persist($script);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/site/scripts/{id}', name: 'be_get_site_scripts', methods:"GET")]
    public function getSiteScripts(Request $request, string $id): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Site::class
        );

        $data = $this->context->getEntityByIdentifier($id);
        $result = [];

        $scripts = ArrayProvider::getObjectsAsArray($data->__get('siteScripts'));
        foreach ($scripts as $script) {
            if (!DataHandler::doesKeyExists($script['area'], $result)) {
                $result[$script['area']] = [];
            }
            $result[$script['area']][] = $script;
        }
        foreach ($result as $resultKey => $subResult) {
            $result[$resultKey] = DataHandler::sortItemsByField($subResult, 'position');
        }

        return $this->prepareReturn(
            [
                'success' => true,
                'data' => DataHandler::getArrayValues($result)
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

    #[Route('/be/api/custom/siteScript/adopt', name: 'be_adopt_site_script', methods:"PUT")]
    public function adoptSiteScript(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            SiteScript::class
        );
        $data = $this->context->getEntityByIdentifier($this->context->getRequestData()['id']);
        $site = $data->__get('site');
        $domain = $site->__get('domain');

        foreach ($domain->__get('sites') as $domainSite) {
            if ($site->__get('id') === $domainSite->__get('id')) {
                continue;
            }
            $position = 0;

            foreach ($domainSite->__get('siteScripts') as $siteScript) {
                if ($data->__get('area') === $siteScript->__get('area') && $siteScript->__get('position') >= $position) {
                    $position = $siteScript->__get('position') + 1;
                }
            }

            $newSiteScript = new SiteScript();
            $newSiteScript->__set('site', $domainSite);
            $newSiteScript->__set('script', $data->__get('script'));
            $newSiteScript->__set('area', $data->__get('area'));
            $newSiteScript->__set('position', $position);

            $this->context->getEntityManager()->persist($newSiteScript);
            $this->context->getEntityManager()->flush();
        }

        return $this->prepareReturn(['success' => true]);
    }
}