<?php

namespace Smug\Core\Entity\Connection\Schema;

/**
 * @internal
 */
class TableDiff extends \Doctrine\DBAL\Schema\TableDiff
{
    /**
     * Platform specific table options
     *
     * @var array
     */
    protected $tableOptions = [];

    /**
     * Getter for table options.
     */
    public function getTableOptions(): array
    {
        return $this->tableOptions;
    }

    /**
     * Setter for table options
     */
    public function setTableOptions(array $tableOptions): TableDiff
    {
        $this->tableOptions = $tableOptions;

        return $this;
    }

    /**
     * Check if a table options has been set.
     */
    public function hasTableOption(string $optionName): bool
    {
        return array_key_exists($optionName, $this->tableOptions);
    }

    public function getTableOption(string $optionName): string
    {
        if ($this->hasTableOption($optionName)) {
            return (string)$this->tableOptions[$optionName];
        }

        return '';
    }

    public function isEmpty(): bool
    {
        return count($this->addedColumns) === 0
            && count($this->changedColumns) === 0
            && count($this->removedColumns) === 0
            && count($this->renamedColumns) === 0
            && count($this->addedIndexes) === 0
            && count($this->changedIndexes) === 0
            && count($this->removedIndexes) === 0
            && count($this->renamedIndexes) === 0
            && count($this->addedForeignKeys) === 0
            && count($this->changedForeignKeys) === 0
            && count($this->removedForeignKeys) === 0
            && $this->tableOptions === [];
    }
}
