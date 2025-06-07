<?php

namespace Smug\Core\DataAbstractionLayer;

use Doctrine\DBAL\Schema\Table;
use ReflectionClass;
use ReflectionProperty;
use Smug\Core\DataAbstractionLayer\ColumnBuilder\ColumnBuilder;
use Smug\Core\Entity\Base\Attribute\ExtensionTarget;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class SchemaExtensionBuilder
{
    public static function gatherTableColumn(
        ReflectionProperty $property,
        Table $table
    ): Table {
		return ColumnBuilder::createColumnFromReflection($property, $table);
    }

	public static function getSchemaExtensions(): array
	{
		$extensions = [];
		$bundles = require __DIR__ . '/../../config/bundles.php';

		foreach ($bundles as $bundleKey => $bundle) {
			$namespace = self::getBundleNameSpace($bundleKey);

			$classesInNamespace = DataHandler::getClassesInNamespace($namespace . '\\', 'Entity' . DIRECTORY_SEPARATOR . 'Schema');
			
			foreach ($classesInNamespace as $extensionClass) {
				$class = new ReflectionClass($extensionClass);

				foreach ($class->getProperties() as $property) {
					$targetEntity = self::getPropertyExtensionTarget($property);

					if (!DataHandler::doesKeyExists($targetEntity, $extensions)) {
						$extensions[$targetEntity] = [];
					}
					$extensions[$targetEntity][] = $property;
				}
			}
		}

		return $extensions;
	}

	public static function getSchemaExtensionForClassAndName(string $class, string $name): ?ReflectionProperty
	{
		$extensions = self::getSchemaExtensions();

		if (DataHandler::doesKeyExists($class, $extensions)) {
			return self::getProperty($extensions[$class], $name);
		}

		return null;
	}

	protected static function getProperty(array $properties, string $name): ?ReflectionProperty
	{
		foreach ($properties as $property) {
			if ($property->getName() === $name) {
				return $property;
			}
		}
		return null;
	}

	protected static function getPropertyExtensionTarget(ReflectionProperty $property): ?string
	{
		foreach ($property->getAttributes(ExtensionTarget::class) as $attribute) {
			if ($attribute->getName() === ExtensionTarget::class) {
				return DataHandler::getFirstArrayElement($attribute->getArguments());
			}
		}
		return null;
	}

	protected static function getBundleNameSpace(string $bundleName): string
	{
		$parts = DataHandler::explodeArray('\\', $bundleName);

		return $parts[0] . '\\' . $parts[1];
	}
}
