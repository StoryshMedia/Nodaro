<?php

namespace Smug\AdministrationBundle\Service\Components\Factories;

use ReflectionClass;
use ReflectionProperty;
use Smug\AdministrationBundle\Interface\View\Items\FieldInterface;
use Smug\AdministrationBundle\Interface\View\Items\RowInterface;
use Smug\AdministrationBundle\Interface\View\Items\TabInterface;
use Smug\AdministrationBundle\Interface\View\ViewBuilderInterface;
use Smug\AdministrationBundle\Interface\View\ViewInterface;
use Smug\AdministrationBundle\Service\Components\Factories\View\Row;
use Smug\AdministrationBundle\Service\Components\Factories\View\Tab;
use Smug\AdministrationBundle\Service\Components\Factories\View\View;
use Smug\AdministrationBundle\Trait\ActiveFieldTrait;
use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ViewBuilder implements ViewBuilderInterface {
    use ActiveFieldTrait;

    public static function build(array $configuration, Context $context): ViewInterface {
        $view = new View();
        $rc = null;
        $view->setContext($context);
        $view->setConfig($configuration['config']);
        
        if (($view->getConfig()['model'] ?? false) !== false) {
            $modelArray = DataHandler::explodeArray('\\', $view->getConfig()['model']);
            $view->addConfigItem('class', DataHandler::getLastArrayElement($modelArray));
            
            $rc = new ReflectionClass($view->getConfig()['model']);
        }

        if (DataHandler::doesKeyExists('tabs', $configuration)) {
            foreach ($configuration['tabs'] as $tab) {
                $view = self::addTab($view, $tab, $rc);
            }
        }

        return LinkBuilder::build($view);
    }

    public static function addTab(View $view, array $tabConfiguration, ?ReflectionClass $rc = null): View {
        $view->addTab(self::buildTab($tabConfiguration, $rc));

        return $view;
    }

    public static function addRow(Tab $tab, array $rowConfiguration, ?ReflectionClass $rc = null): Tab {
        $tab->addRow(self::buildRow($rowConfiguration, $rc));

        return $tab;
    }

    public static function addField(Row $row, string $identifier, ?ReflectionClass $rc = null): Row {
        $row->addField(self::buildField(self::getFieldConfig($identifier, $rc->getProperties())));

        return $row;
    }

    public static function buildTab(array $tabConfiguration, ?ReflectionClass $rc = null): TabInterface {
        $tab = new Tab();

        $tab->setHeadline($tabConfiguration['headline']);
        $tab->setIcon($tabConfiguration['icon'] ?? 'IconHome');

        if (DataHandler::doesKeyExists('rows', $tabConfiguration)) {
            foreach ($tabConfiguration['rows'] as $row) {
                $tab = self::addRow($tab, $row, $rc);
            }
        }

        return $tab;
    }

    public static function buildRow(array $rowConfiguration, ?ReflectionClass $rc = null): RowInterface {
        $row = new Row();

        $row->setClass($rowConfiguration['class'] ?? '');
        try {
            if (DataHandler::doesKeyExists('fields', $rowConfiguration)) {
                foreach ($rowConfiguration['fields'] as $field) {
                    if (DataHandler::isArray($field)) {
                        $row->addField(self::buildField($field));
                        continue;
                    }
                    $row = self::addField($row, $field, $rc);
                }
            }
        } catch (\Throwable $e) {
            dd($rowConfiguration);
        }
        

        return $row;
    }

    public static function buildField(array $fieldConfiguration): ?FieldInterface {
        $fieldClass = self::getField($fieldConfiguration['type']);

        $field = new $fieldClass();

        $field->setType($fieldConfiguration['type']);
        $field->setPlaceholder($fieldConfiguration['placeholder'] ?? '');
        $field->setIdentifier($fieldConfiguration['identifier'] ?? '');
        $field->setConfig($fieldConfiguration['config'] ?? []);

        return $field;
    }

    public static function getFieldConfig(string $identifier, array $properties, string $prefix = '') : array
    {
        /** @var ReflectionProperty $property */
        foreach ($properties as $property) {
            if (DataHandler::isStringInString($identifier, '.')) {
                $identifierParts = DataHandler::explodeArray('.', $identifier);
                if ($property->getName() === $identifierParts[0]) {
                    foreach ($property->getAttributes() as $attribute) {
                        if (!DataHandler::isEmpty($attribute->getArguments()['targetEntity'] ?? '')) {
                            $rc = new ReflectionClass($attribute->getArguments()['targetEntity']);
                            return self::getFieldConfig($identifierParts[1], $rc->getProperties(), $identifierParts[0]);
                        }
                    }
                }
            } else {
                if ($property->getName() === $identifier) {
                    foreach ($property->getAttributes() as $attribute) {
                        if ($attribute->getName() === BackendField::class) {
                            $config = $attribute->getArguments()['config'] ?? [];
                            $config['identifier'] = (!DataHandler::isEmpty($prefix)) ? $prefix . '.' . $identifier : $identifier;
                            
                            return $config;
                        }
                    }
                }
            }
        }

        return [];
    }
}
