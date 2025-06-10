<?php

namespace Smug\FrontendBundle\Controller\Backend\Api\Module;

use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Exception\Base\NotAllowedException;
use Smug\FrontendBundle\Controller\Frontend\Api\Base\FeBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Mail\SendMail;
use Smug\Core\Service\Base\Service\ListBaseService;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\ContentItemModuleTab\ContentItemModuleTab;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\FrontendBundle\Entity\ModuleField\ModuleField;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\Data\ContentItemModuleDataLoadedEvent;
use Smug\FrontendBundle\Event\FrontendEvents;
use Smug\FrontendBundle\Service\Frontend\Renderer\FrontendModuleRenderer;
use Smug\FrontendBundle\Service\Frontend\Slug\SlugService;
use Smug\FrontendBundle\Service\Module\Add\AddService;
use Smug\FrontendBundle\Service\Navigation\Update\UpdateService;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\RouterInterface;

class DataController extends FeBaseController
{
    private static array $systemFiles = [
        'dynamicFrontendComponents'
    ];

    private FrontendModuleRenderer $renderer;

    public function __construct(
        FrontendModuleRenderer $renderer,
        protected RouterInterface $router,
        Context $context,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        SendMail $mail
    ) {
        $this->renderer = $renderer;    
        parent::__construct(
            $router,
            $context,
            $em,
            $dispatcher,
            $mail
        );
    }

    #[Route('/be/api/custom/module/scan', name: 'be_module_scan_for_modules', methods:"POST")]
    public function scan(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $finder = new Finder();
        $finder->directories()->in($this->context->getKernel()->getProjectDir() . "/bundle/")->depth(0);

        foreach ($finder as $file) {
            $bundleFinder = new Finder();
            $bundleFinder->directories()->in($file->getRealPath())->depth(0);

            foreach ($bundleFinder as $bundle) {
                if (!DataHandler::proofDir($bundle->getRealPath() . DIRECTORY_SEPARATOR . 'config/frontend/module')) {
                    continue;
                }

                $moduleFinder = new Finder();
                $moduleFinder->files()->in($bundle->getRealPath() . DIRECTORY_SEPARATOR . 'config/frontend/module');

                foreach ($moduleFinder as $module) {
                    if ($module->getExtension() !== 'json' || DataHandler::isInArray($module->getFilenameWithoutExtension(), self::$systemFiles)) {
                        continue;
                    }

                    $moduleData = DataHandler::getJsonDecode(
                        DataHandler::getFile($module->getRealPath()),
                        true
                    );

                    $moduleData = $this->dispatchModuleDataLoadedEvent($moduleData);

                    $exsistingModule = $this->context->getEntityByIdentifier($moduleData['identifier'], 'identifier');

                    if (!DataHandler::isEmpty($exsistingModule)) {
                        continue;
                    }

                    $newModule = new Module();
                    $newModule->__set('title', $moduleData['title']);
                    $newModule->__set('configFile', $module->getRealPath());
                    $newModule->__set('identifier', $moduleData['identifier']);
                    $newModule->__set('category', $moduleData['category']);
                    $newModule->__set('type', $moduleData['type'] ?? 'contentElement');
                    $newModule->__set('multi', $moduleData['multi'] ?? false);
                    $newModule->__set('installed', false);
                    $newModule->__set('active', false);
                    $newModule->__set('description', $moduleData['description']['de']);
                    $newModule->__set('template', DataHandler::getJsonEncode($moduleData['settings']['template'] ?? []));
                    $newModule->__set('scripts', DataHandler::getJsonEncode($moduleData['settings']['scripts'] ?? []));
                    
                    $this->context->getEntityManager()->persist($newModule);
                    $this->context->getEntityManager()->flush($newModule);
                }
            }
        }

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/module/install', name: 'be_module_install', methods:"PUT")]
    public function install(Request $request, AddService $addService): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $module = $this->context->getMainEntity();
        $module->__set('installed', true);

        $this->context->getEntityManager()->persist($module);
        $this->context->getEntityManager()->flush();

        $configFilePath = $module->__get('configFile');
        $modulePath = DataHandler::getFileLocationPath($configFilePath);
        
        $moduleData = DataHandler::getJsonDecode(
            DataHandler::getFile($configFilePath),
            true
        );

        $moduleData = $this->dispatchModuleDataLoadedEvent($moduleData);

        $this->context->addRepository('field', ModuleField::class);

        $addService->installModuleFields($moduleData['settings']['fields'] ?? [], $this->context, $module);

        if (DataHandler::doesKeyExists('plugin', $moduleData['settings'])) {
            $addService->installModuleFields($moduleData['settings']['plugin']['fields'] ?? [], $this->context, $module, true);
        }

        if (DataHandler::proofDir($modulePath . DIRECTORY_SEPARATOR . 'assets')) {
            $publicModulePath = $this->context->getKernel()->getProjectDir() . DIRECTORY_SEPARATOR . 'public/Resources/elements' . DIRECTORY_SEPARATOR . $module->__get('identifier') . DIRECTORY_SEPARATOR;

            DataHandler::makeDir($publicModulePath);
            if (DataHandler::proofDir($modulePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images')) {
                $publicModuleImagesPath = $publicModulePath . 'images' . DIRECTORY_SEPARATOR;

                DataHandler::copyFolder($modulePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR, $publicModuleImagesPath);
            }
        }

        if (!DataHandler::isEmpty($moduleData['settings']['tabs'] ?? [])) {
            $addService->installTabs($moduleData['settings']['tabs'], $this->context, $module);
        }

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/module/update', name: 'be_module_update', methods:"PUT")]
    public function update(Request $request, AddService $addService): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $module = $this->context->getMainEntity();

        $configFilePath = $module->__get('configFile');
        $modulePath = DataHandler::getFileLocationPath($configFilePath);
        
        $moduleData = DataHandler::getJsonDecode(
            DataHandler::getFile($configFilePath),
            true
        );

        $moduleData = $this->dispatchModuleDataLoadedEvent($moduleData);

        $module->__set('title', $moduleData['title']);
        $module->__set('category', $moduleData['category']);
        $module->__set('type', $moduleData['type'] ?? 'contentElement');
        $module->__set('multi', $moduleData['multi'] ?? false);
        $module->__set('description', $moduleData['description']['de']);
        $module->__set('template', DataHandler::getJsonEncode($moduleData['settings']['template'] ?? []));
        $module->__set('scripts', DataHandler::getJsonEncode($moduleData['settings']['scripts'] ?? []));
        
        $this->context->getEntityManager()->persist($module);
        $this->context->getEntityManager()->flush();

        $this->context->addRepository('field', ModuleField::class);
        $this->context->addRepository('contentItemModuleField', ContentItemModuleField::class);
        $this->context->addRepository('contentItemModule', ContentItemModule::class);

        $addService->installModuleFields($moduleData['settings']['fields'] ?? [], $this->context, $module);

        if (DataHandler::doesKeyExists('plugin', $moduleData['settings'])) {
            $addService->installModuleFields($moduleData['settings']['plugin']['fields'] ?? [], $this->context, $module, true);
        }

        if (DataHandler::proofDir($modulePath . DIRECTORY_SEPARATOR . 'assets')) {
            $publicModulePath = $this->context->getKernel()->getProjectDir() . DIRECTORY_SEPARATOR . 'public/Resources/elements' . DIRECTORY_SEPARATOR . $module->__get('identifier') . DIRECTORY_SEPARATOR;

            if (DataHandler::proofDir($modulePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images')) {
                $publicModuleImagesPath = $publicModulePath . 'images' . DIRECTORY_SEPARATOR;

                DataHandler::copyFolder($modulePath . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR, $publicModuleImagesPath);
            }
        }

        $usedItems = $this->context->getByIdentifier($module, 'module', 'contentItemModule');

        foreach ($usedItems as $usedItem) {
            $addService->installModuleFields($moduleData['settings']['fields'] ?? [], $this->context, $usedItem, false, 'contentItemModuleField');
    
            if (DataHandler::doesKeyExists('plugin', $moduleData['settings'])) {
                $addService->installModuleFields($moduleData['settings']['plugin']['fields'] ?? [], $this->context, $usedItem, true, 'contentItemModuleField');
            }

            if (!DataHandler::isEmpty(DataHandler::getJsonDecode($module->__get('template'), true)['classes'] ?? [])) {
                $item = $usedItem->__get('content');

                if (!DataHandler::isEmpty($item)) {
                    $item->__set('templateClasses', DataHandler::getJsonEncode(DataHandler::getJsonDecode($module->__get('template'), true)['classes']));

                    $this->context->getEntityManager()->persist($item);
                    $this->context->getEntityManager()->flush();
                }
            }
        }

        if (!DataHandler::isEmpty($moduleData['settings']['tabs'] ?? [])) {
            $addService->installTabs($moduleData['settings']['tabs'], $this->context, $module);
        }

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/module/deinstall', name: 'be_module_deinstall', methods:"PUT")]
    public function deinstall(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $module = $this->context->getMainEntity();
        $module->__set('installed', false);
        $module->__set('active', false);

        $this->context->getEntityManager()->persist($module);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/module/rerender', name: 'be_module_rerender', methods:"POST")]
    public function rerender(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ContentItem::class
        );

        return $this->prepareReturn($this->renderer->render($this->context->getRequestData(), true));
    }

    #[Route('/be/api/custom/module/plugin/config/refresh', name: 'be_refresh_plugin_settings', methods:"POST")]
    public function pluginSettingsRefresh(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ContentItem::class
        );

        return $this->prepareReturn(
            $this->renderer->render($this->context->getRequestData()['data'], true)
        );
    }

    #[Route('/be/api/custom/module/tab', name: 'be_add_module_tab', methods:"POST")]
    public function addModuleTab(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ContentItemModule::class
        );
        $this->context->addRepository('module', ContentItemModule::class);

        $tab = new ContentItemModuleTab();

        $tab->__set('module', $this->context->getEntityByIdentifier(
            $this->context->getRequestData()['data']['module']['id']
        ));
        $this->context->getEntityManager()->persist($tab);
        $this->context->getEntityManager()->flush();

        foreach ($this->context->getRequestData()['data']['fields'] as $fieldData) {
            $fieldObject = new ContentItemModuleField();
            
            $fieldObject->__set('identifier', $fieldData['identifier']);
            $fieldObject->__set('placeholder', $fieldData['placeholder'] ?? '');
            $fieldObject->__set('description', $fieldData['description'] ?? '');
            $fieldObject->__set('type', $fieldData['type']);
            $fieldObject->__set('tab', $tab);
            $fieldObject->__set('isPlugin', $fieldData['plugin'] ?? false);
            $fieldObject->__set('value', $fieldData['value'] ?? '');
            $fieldObject->__set('config', DataHandler::getJsonEncode($fieldData['config'] ?? []));
            $fieldObject->__set('settings', DataHandler::getJsonEncode($fieldData['settings'] ?? []));
            $fieldObject->__set('classes', DataHandler::getJsonEncode($fieldData['classes'] ?? []));

            $this->context->getEntityManager()->persist($fieldObject);
            $this->context->getEntityManager()->flush();

            $tab->__add('fields', $fieldObject);
            $this->context->getEntityManager()->persist($tab);
            $this->context->getEntityManager()->flush();
        }

        return $this->prepareReturn(
            $tab->toArray()
        );
    }

    #[Route('/be/api/custom/module/plugin/template', name: 'be_refresh_plugin_settings_template', methods:"POST")]
    public function pluginSettingsTemplateRender(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            ContentItem::class
        );

        return $this->prepareReturn(
            [
                'template' => '<div class="frontend">' . $this->context->getRequestData()['template'] . '</div>'
            ]
        );
    }

    #[Route('/be/api/custom/module/activate', name: 'be_module_activate', methods:"PUT")]
    public function activate(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $module = $this->context->getMainEntity();
        $module->__set('active', true);

        $this->context->getEntityManager()->persist($module);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/module/deactivate', name: 'be_module_deactivate', methods:"PUT")]
    public function deactivate(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Module::class
        );

        $module = $this->context->getMainEntity();
        $module->__set('active', false);

        $this->context->getEntityManager()->persist($module);
        $this->context->getEntityManager()->flush();

        return $this->prepareReturn(['success' => true]);
    }

    #[Route('/be/api/custom/site/slug/generate', name: 'be_slug_regenerate', methods:"PUT")]
    public function regenetrate(Request $request, SlugService $service): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Site::class
        );

        return $this->prepareReturn(['slug' => $service->create($this->context)]);
    }

    #[Route('/be/api/custom/site/domain/sites/{id}', name: 'be_get_flat_sites', methods:"GET")]
    public function getSites(
        $id,
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Site::class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper('site'));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $this->context->setIdentifier($id);
        $site = $service->getSingle($this->context);

        if (DataHandler::doesKeyExists('message', $site)) {
            return $this->prepareReturn($site);
        }

        $context = ServiceGenerationFactory::createInstance(Context::class);
        $context->buildFromData(
            $site['domain'],
            Domain::class
        );

        $context->setConfigItem('field', 'sites');
        $context->setConfigItem('nested', false);
        $context->setIdentifier($site['domain']['id']);

        return $this->prepareReturn($service->getSubData($context));
    }

    #[Route('/be/api/custom/site/domain/sites', name: 'be_get_flat_sitesfrom_all_domains', methods:"GET")]
    public function getSitesFromAllDomains(
        Request $request,
        ListBaseService $service
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Domain::class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper('site'));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        $siteData = [];

        $domains = $this->context->getAllEntities();

        foreach ($domains as $domain) {
            $domain = $domain->toArray();
            $context = ServiceGenerationFactory::createInstance(Context::class);
            $context->buildFromData(
                $domain,
                Domain::class
            );

            $context->setConfigItem('fields', ['title', 'slug']);
            $context->setConfigItem('field', 'sites');
            $context->setConfigItem('nested', false);
            $context->setIdentifier($domain['id']);

            $siteData[] = [
                'domain' => [
                    'title' => $domain['title'],
                    'url' => $domain['url']
                ],
                'sites' => $service->getSubData($context)
            ];
        }

        return $this->prepareReturn($siteData);
    }

    #[Route('/be/api/custom/site/domain/sites/tree', name: 'be_save_site_tree', methods:"PUT")]
    public function saveSiteTree(
        Request $request,
        UpdateService $service
    ): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            Site::class
        );

        try {
            $this->context->isAllowed('read', DataHandler::getFirstCapitalUpper('site'));
        } catch (NotAllowedException $exception) {
            return new JsonResponse(ExceptionProvider::getException($exception));
        }

        return $this->prepareReturn($service->saveTree($this->context));
    }

    #[Route('/be/api/custom/content/item/adopt', name: 'be_adopt_content_items', methods:"PUT")]
    public function adoptSiteScript(Request $request): JsonResponse
    {
        $this->context->buildFromRequest(
            $request,
            EntityGenerator::getGeneratedEntity(ContentItem::class)
        );
        $this->context->addRepository('site', EntityGenerator::getGeneratedEntity(Site::class));

        $data = $this->context->getEntityByIdentifier($this->context->getRequestData()['item']['id']);

        foreach ($this->context->getRequestData()['sites'] as $siteArray) {
            $site = $this->context->getEntityByIdentifier($siteArray['id'], 'id', 'site');

            $moduleClass = EntityGenerator::getGeneratedEntity(ContentItemModule::class);
            $module = new $moduleClass();
            $module->__set('module',  $data->__get('module')->__get('module'));

            $this->context->getEntityManager()->persist($module);
            $this->context->getEntityManager()->flush();

            foreach ($data->__get('module')->__get('fields') as $field) {
                $moduleFieldClass = EntityGenerator::getGeneratedEntity(ContentItemModuleField::class);
                $newField = new $moduleFieldClass();

                $newField->__set('type', $field->__get('type'));
                $newField->__set('module', $module);
                $newField->__set('config', $field->__get('config'));
                $newField->__set('settings', $field->__get('settings'));
                $newField->__set('classes', $field->__get('classes'));
                $newField->__set('placeholder', $field->__get('placeholder'));
                $newField->__set('description', $field->__get('description'));
                $newField->__set('parentId', $field->__get('parentId'));
                $newField->__set('value', $field->__get('value'));
                $newField->__set('isPlugin', $field->__get('isPlugin'));
                $newField->__set('identifier', $field->__get('identifier'));

                $this->context->getEntityManager()->persist($newField);
                $this->context->getEntityManager()->flush();

                $module->__add('fields', $newField);
            }


            $queryBuilder = $this->context->getEntityManager()->createQueryBuilder();

            $queryBuilder->select('c')
                ->from(EntityGenerator::getGeneratedEntity(ContentItem::class), 'c')
                ->andWhere('c.position >= :position')
                ->andWhere('c.site = :site')
				->setParameter('position', DataHandler::getIntFromString($siteArray['position']), ParameterType::INTEGER)
				->setParameter('site', $site->__get('id'), ParameterType::STRING);

            $items = $queryBuilder->getQuery()->getResult();

            $position = DataHandler::getIntFromString($siteArray['position']);
            $position++;
            foreach ($items as $item) {
                $item->__set('position', $position);

                $this->context->getEntityManager()->persist($item);
                $this->context->getEntityManager()->flush();
                $position++;
            }

            $itemClass = EntityGenerator::getGeneratedEntity(ContentItem::class);
            $newItem = new $itemClass();
            $newItem->__set('site', $site);
            $newItem->__set('entry', $data->__get('entry'));
            $newItem->__set('title', $data->__get('title'));
            $newItem->__set('module', $data->__get('module'));
            $newItem->__set('additionalClasses', $data->__get('additionalClasses'));
            $newItem->__set('templateClasses', $data->__get('templateClasses'));
            $newItem->__set('rowColumn', $data->__get('rowColumn'));
            $newItem->__set('parentId', $data->__get('parentId'));
            $newItem->__set('position', DataHandler::getIntFromString($siteArray['position']));

            $this->context->getEntityManager()->persist($newItem);
            $this->context->getEntityManager()->flush();

            $module->__set('content',  $newItem);
            $this->context->getEntityManager()->persist($module);
            $this->context->getEntityManager()->flush();
        }

        return $this->prepareReturn(['success' => true]);
    }

    public function dispatchModuleDataLoadedEvent(array $moduleData): array
    {
        $moduleData = $this->dispatchData(
            $moduleData,
            $this->context,
            ContentItemModuleDataLoadedEvent::class,
            '',
            FrontendEvents::FRONTEND_CONTENT_ITEM_MODULE_DATA_LOADED
        );

        return $moduleData;
    }
}