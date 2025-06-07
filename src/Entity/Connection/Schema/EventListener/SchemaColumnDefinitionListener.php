<?php

namespace Smug\Core\Entity\Connection\Schema\EventListener;

use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class SchemaColumnDefinitionListener
{
    /**
     * Listener for column definition events. This intercepts definitions
     * for custom doctrine types and builds the appropriate Column Object.
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $event)
    {
        $tableColumn = $event->getTableColumn();
        $tableColumn = array_change_key_case($tableColumn, CASE_LOWER);

        $dbType = $this->getDatabaseType($tableColumn['type']);
        if ($dbType !== 'enum' && $dbType !== 'set') {
            return;
        }

        $column = $this->getEnumerationTableColumnDefinition(
            $tableColumn,
            $event->getConnection()->getDatabasePlatform()
        );

        $event->setColumn($column);
        $event->preventDefault();
    }

    /**
     * Build a Doctrine column object for TYPE/TYPE columns.
     *
     * @throws \Doctrine\DBAL\Exception
     * @todo: The $tableColumn source currently only support MySQL definition style.
     */
    protected function getEnumerationTableColumnDefinition(array $tableColumn, AbstractPlatform $platform): Column
    {
        $options = [
            'length' => $tableColumn['length'] ?? null,
            'unsigned' => false,
            'fixed' => false,
            'default' => $tableColumn['default'] ?? null,
            'notnull' => ($tableColumn['null'] ?? '') !== 'YES',
            'scale' => null,
            'precision' => null,
            'autoincrement' => false,
            'comment' => $tableColumn['comment'] ?? null,
        ];

        $dbType = $this->getDatabaseType($tableColumn['type']);
        $doctrineType = $platform->getDoctrineTypeMapping($dbType);

        $column = new Column($tableColumn['field'] ?? '', Type::getType($doctrineType), $options);
        $column->setPlatformOption('unquotedValues', $this->getUnquotedEnumerationValues($tableColumn['type']));

        return $column;
    }

    /**
     * Extract the field type from the definition string
     */
    protected function getDatabaseType(string $typeDefinition): string
    {
        $dbType = strtolower($typeDefinition);
        $dbType = strtok($dbType, '(), ');

        return $dbType;
    }

    protected function getUnquotedEnumerationValues(string $typeDefinition): array
    {
        $valuesDefinition = preg_replace('#^(enum|set)\((.*)\)\s*$#i', '$2', $typeDefinition) ?? '';
        $quoteChar = $valuesDefinition[0];
        $separator = $quoteChar . ',' . $quoteChar;

        $valuesDefinition = preg_replace(
            '#' . $quoteChar . ',\s*' . $quoteChar . '#',
            $separator,
            $valuesDefinition
        ) ?? '';

        $values = explode($quoteChar . ',' . $quoteChar, substr($valuesDefinition, 1, -1));

        return array_map(
            static function (string $value) use ($quoteChar) {
                return str_replace($quoteChar . $quoteChar, $quoteChar, $value);
            },
            $values
        );
    }
}
