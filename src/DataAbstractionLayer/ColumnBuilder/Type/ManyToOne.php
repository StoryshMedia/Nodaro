<?php

namespace Smug\Core\DataAbstractionLayer\ColumnBuilder\Type;

use Doctrine\DBAL\Schema\Table;
use Doctrine\ORM\Mapping\Table as MappingTable;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne as BaseManyToOne;
use ReflectionClass;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\Type\TypeInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ManyToOne implements TypeInterface
{
	public static function transform(ReflectionProperty $property, Table $table): Table
	{
		$attributes = $property->getAttributes(BaseManyToOne::class);

        $joinColumnAttributes = $property->getAttributes(JoinColumn::class);
        $joinColumn = $joinColumnAttributes[0]->newInstance() ?? null;

        $columnName = $joinColumn->name ?? strtolower($property->getName() . "_id");

        if ($table->hasColumn($columnName)) {
            return $table;
        }

        $targetEntity = new ReflectionClass($attributes[0]->newInstance()->targetEntity);
        $referencedTable = self::getReferenceTableName($targetEntity);

        $table->addColumn($columnName, "guid", [
            'notnull' => !$joinColumn->nullable,
        ]);

        $table->addForeignKeyConstraint(
            $referencedTable, 
            [$columnName], 
            [$joinColumn->referencedColumnName ?? "id"]
        );

        return $table;
	}

    protected static function getReferenceTableName(ReflectionClass $targetEntity): string
    {
        $table = $targetEntity->getAttributes(MappingTable::class);

        if (!DataHandler::isEmpty($table)) {
            $table[0]->getArguments()[0];

            return $table[0]->getArguments()[0];
        }
        
        return DataHandler::getLowerString($targetEntity->getShortName());
    }
}
