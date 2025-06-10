<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Exception;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Twig\Environment;

class FrontendModuleRenderer
{
    protected Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    
    public function render(array $module, bool $refresh = false): array
    {
        try {
            $moduleConfigFile = DataHandler::getJsonDecode(DataHandler::getFile(self::getConfigFile($module)), true);
        } catch (Exception $e) {
            dd($module);
        }
        $pluginSettings = FieldEnricher::enrichPluginFields($module);
        $data = DataHandler::mergeArray(
            FieldEnricher::enrichFields($module, $moduleConfigFile),
            [
                'templatePath' => $moduleConfigFile['settings']['template']['frontend']['templatePath'],
                'pluginSettings' => $pluginSettings,
                'pluginSettingsJson' => DataHandler::getJsonEncode(FieldEnricher::enrichPluginFields($module))
            ]
        );

        $tabs = [];
        foreach ($module['module']['tabs'] as $tab) {
            $tabs[] = FieldEnricher::enrichFields($tab, $moduleConfigFile);
        }
        $data['tabs'] = $tabs;

        $data = DataHandler::mergeArray(
            ['contentEditable' => FieldEnricher::getContentEditableFields($module, $module['module']['tabs'])],
            $data
        );
        $data['id'] = $module['id'];
        
        if (DataHandler::isString($module['additionalClasses'])) {
            $module['additionalClasses'] = DataHandler::getJsonDecode($module['additionalClasses'], true);
        }

        $data['additionalClasses'] = DataHandler::implodeArray(' ', $module['additionalClasses']);
        $data['children'] = $module['children'];
        
        if (!DataHandler::isEmpty($data['children'])) {
            $columns = DataHandler::groupByField($data['children'], 'rowColumn');
            $itemColumns = [];

            foreach ($columns as $column) {
                $column = DataHandler::sortItemsByField($column, 'position');
                $itemColumns[] = $column;
            }

            $data['children'] = $itemColumns;
        }

        if (!DataHandler::isArray($module['templateClasses'])) {
            $module['templateClasses'] = DataHandler::getJsonDecode($module['templateClasses'], true);
        }

        foreach ($module['templateClasses'] as $classKey => $templateClass) {
            if (!DataHandler::isArray($templateClass['value'])) {
                $module['templateClasses'][$classKey]['value'] = DataHandler::explodeArray(' ', $templateClass['value']);
            }
        }

        $data['template'] = $module['templateClasses'];

        if (!DataHandler::isEmpty($moduleConfigFile['settings']['previewData'] ?? [])) {
            $data = DataHandler::mergeArray($moduleConfigFile['settings']['previewData'], $data);
        }

        if(!$refresh && DataHandler::doesKeyExists('variables', $module)) {
            $data = DataHandler::mergeArray($data, $module['variables']);
        }

        try {
            $module['rendered'] = $this->getRenderedTemplate($data);
        } catch (Exception $e) {
            dd($e);
        }
        
        return $module;
    }

    public function getRenderedTemplate(array $data): string
    {
        return $this->twig->render('@SmugAdministration/backend/module/index.html.twig', $data);
    }

    public static function getConfigFile(array $module): string
    {
        return $module['configFile'] ?? $module['module']['module']['configFile'];
    }
}
