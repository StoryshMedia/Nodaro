<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Events\Frontend\Site\ContentItemLoadedEvent;
use Smug\Core\Events\Frontend\Site\PluginSettingsLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

class SiteContentBuilder
{
    public static function getLoadedContentItemsInfo(array $contentItems): array {
        $info = [];

        foreach ($contentItems as $contentItem) {
            $info[] = $contentItem['module']['module']['identifier'];
        }
        
        return $info;
    }

    public static function getContentItems($site, array $additional = [], ?EventDispatcherInterface $dispatcher = null, ?Context $context = null): array
    {
        $siteArray = (DataHandler::isInstanceOf($site, EntityGenerator::getGeneratedEntity(Site::class))) ? $site->toArray() : $site;

        $siteArray['contentItems'] = DataHandler::getTree($siteArray['contentItems']);
        $siteArray['contentItems'] = self::enrichContentItems($siteArray['contentItems'], $additional, $dispatcher, $context);
        $siteArray['contentItems'] = DataHandler::sortItemsByField($siteArray['contentItems'], 'position');

        return $siteArray['contentItems'];
    }

    public static function enrichContentItems(array $contentItems, $additional, $dispatcher, $context): array {
        foreach ($contentItems as $contentKey => $contentItem) {
            $item = $contentItems[$contentKey];
            $item = self::enrichContentItem($item, $contentItem, $additional, $dispatcher);
            
            if (DataHandler::getArrayLength($item['children'] ?? [])) {
                $columns = DataHandler::groupByField($item['children'], 'rowColumn');
                $itemColumns = [];

                foreach ($columns as $column) {
                    $column = self::enrichContentItems($column, $additional, $dispatcher, $context);
                    $column = DataHandler::sortItemsByField($column, 'position');
                    $itemColumns[] = $column;
                }

                $item['children'] = $itemColumns;
            }

            $event = new ContentItemLoadedEvent(
                DataHandler::mergeArray($item, ['additionalDataFromRequest' => $additional]),
                $context
            );

            if ($dispatcher) {
                $dispatcher->dispatch(
                    $event,
                    FrontendEvents::FRONTEND_CONTENT_ITEM_LOADED
                );
            }

            $contentItems[$contentKey] = $event->getData();
        }

        return $contentItems;
    }

    public static function enrichContentItem(array $item, array $contentItem, array $additional = [], ?EventDispatcherInterface $dispatcher = null): array
    {
        $item['module']['module']['template'] = DataHandler::getJsonDecode($item['module']['module']['template'], true);
            
        $configFile = DataHandler::getJsonDecode(
            DataHandler::getFile(FrontendModuleRenderer::getConfigFile($contentItem)),
            true
        );

        $item['variables'] = FieldEnricher::enrichFields($contentItem['module'], $configFile);
        $item['variables']['pluginSettings'] = FieldEnricher::enrichPluginFields($contentItem['module']);
        $event = new PluginSettingsLoadedEvent(
            DataHandler::mergeArray($item, ['additionalDataFromRequest' => $additional])
        );

        $tabs = [];
        foreach ($contentItem['module']['tabs'] as $tab) {
            $tabs[] = FieldEnricher::enrichFields($tab, $configFile);
        }
        $item['variables']['tabs'] = $tabs;
        
        $event = new PluginSettingsLoadedEvent(
            DataHandler::mergeArray($item, ['additionalDataFromRequest' => $additional])
        );

        if ($dispatcher) {
            $dispatcher->dispatch(
                $event,
                FrontendEvents::FRONTEND_PLUGIN_SETTINGS_LOADED
            );
        }

        $item = $event->getData();
        $item['variables']['pluginSettingsJson'] = DataHandler::getJsonEncode(
            $item['variables']['pluginSettings']
        );

        $item['variables']['scripts'] = DataHandler::getJsonEncode(
            $contentItem['module']['module']['scripts']
        );

        $item['variables']['id'] = $contentItem['id'];

        if (DataHandler::isString($contentItem['additionalClasses'])) {
            $contentItem['additionalClasses'] = DataHandler::getJsonDecode($contentItem['additionalClasses'], true);
        }

        $contentItem['templateClasses'] = DataHandler::getJsonDecode($contentItem['templateClasses'], true);

        try {
            $item['variables']['additionalClasses'] = DataHandler::implodeArray(' ', $contentItem['additionalClasses']);
        } catch (Throwable $e) {
            dd($e);
        }
        
        $item['variables']['template'] = $contentItem['templateClasses'];

        return $item;
    }
}
