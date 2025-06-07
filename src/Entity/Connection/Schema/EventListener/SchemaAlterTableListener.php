<?php

namespace Smug\Core\Entity\Connection\Schema\EventListener;

use Doctrine\DBAL\Event\SchemaAlterTableEventArgs;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Smug\Core\Entity\Connection\Schema\TableDiff;

class SchemaAlterTableListener
{
    /**
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function onSchemaAlterTable(SchemaAlterTableEventArgs $event)
    {
        /** @var TableDiff $tableDiff */
        $tableDiff = $event->getTableDiff();

        // Original Doctrine TableDiff without table options, continue default processing
        if (!$tableDiff instanceof TableDiff) {
            return false;
        }

        // Table options are only supported on MySQL, continue default processing
        if (!$event->getPlatform() instanceof MySQLPlatform) {
            return false;
        }

        // No changes in table options, continue default processing
        if (count($tableDiff->getTableOptions()) === 0) {
            return false;
        }

        $quotedTableName = $tableDiff->getName($event->getPlatform())->getQuotedName($event->getPlatform());

        // Add an ALTER TABLE statement to change the table engine to the list of statements.
        if ($tableDiff->hasTableOption('engine')) {
            $statement = 'ALTER TABLE ' . $quotedTableName . ' ENGINE = ' . $tableDiff->getTableOption('engine');
            $event->addSql($statement);
        }

        // continue default processing for all other changes.
        return false;
    }
}
