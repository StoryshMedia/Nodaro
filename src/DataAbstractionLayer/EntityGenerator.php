<?php

namespace Smug\Core\DataAbstractionLayer;

use Doctrine\ORM\Mapping\Table;
use ReflectionClass;
use ReflectionProperty;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Base\Structs\BaseStruct;
use Smug\Core\Entity\Base\UserBaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class EntityGenerator
{
	private static string $classTemplate = <<<EOF
<?php 
declare(strict_types=1);

namespace Smug\Core\Entity\Generated;

use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
#useExtends#

#[Entity]
#[Table('#table#')]
class #entityName# #extends#
{
    #properties#
}
EOF;

    private static string $propertyTemplate = <<<EOF
#attributes#
protected $#property#;
EOF;

    private static string $attributeTemplate = <<<EOF
#[\#attribute#(#arguments#)]
EOF;

    public static function generate(string $domain): bool
    {
		$rc = new ReflectionClass($domain);
		$entityData = self::generateEntity($rc);

        DataHandler::writeFile(
            __DIR__ . '/../Entity/Generated/' . $entityData['entityName'] . '.php',
            $entityData['template']
        );
        
		return true;
    }

    public static function getGeneratedEntity(string $class): string
    {
        $entityName = self::getEntityName($class);

        return '\\Smug\Core\Entity\Generated\\' . $entityName;
    }

    private static function generateEntity(ReflectionClass $baseClass): array
    {
        $properties = [];
        $useExtends = [];
        $extends = [];
        $extensionProperties = SchemaExtensionBuilder::getSchemaExtensions();
        $classProperties = $baseClass->getProperties();

        $completeProperties = DataHandler::mergeArray(
            $extensionProperties[$baseClass->getName()] ?? [],
            $classProperties
        );

        foreach ($completeProperties as $classProperty) {
            if ($classProperty->class === BaseModel::class || $classProperty->class === BaseStruct::class || $classProperty->class === UserBaseModel::class) {
                continue;
            }

            $property = self::generateProperty($classProperty);
            if (!$property) {
                continue;
            }
            $properties[] = $property;
        }

        try {
            $tableName = $baseClass->getAttributes(Table::class)[0]->getArguments()[0];
        } catch (\Throwable $e) {
            dd($baseClass->getName());
        }

        if ($baseClass->getParentClass()) {
            $useExtends[] = 'use ' . $baseClass->getParentClass()->getName() . ';';
            $extends[] = DataHandler::getLastArrayElement(
                DataHandler::explodeArray('\\', $baseClass->getParentClass()->getName())
            );
         }

        $extendsString = (DataHandler::getArrayLength($extends) > 0) ? 'extends ' . DataHandler::implodeArray(', ', $extends) : '';

        $domain = explode('\\', $baseClass->getNamespaceName());
        $domain = \array_slice($domain, 0, \count($domain) - 1);
        $domain = implode('\\', $domain);

        $entityName = self::getEntityName($baseClass->getName());

        return [
            'entityName' => $entityName,
            'template' => str_replace(
                ['#entityName#', '#properties#', '#table#', '#useExtends#', '#extends#'],
                [$entityName, implode("\n\n    ", $properties), $tableName, implode("\n", $useExtends), $extendsString],
                self::$classTemplate
            )
        ];
    }

    private static function getEntityName(string $class): string
    {
        if (DataHandler::isStringInString($class, '_')) {
			$class = explode('_', $class);
			$class = array_map('ucfirst', $class);
			$class = implode('', $class);
		}

        $entityName = DataHandler::getReplaceString('\\', '', $class);

        return $entityName;
    }

    private static function generateProperty(ReflectionProperty $property): ?string
    {
		$attributes = [];

		foreach ($property->getAttributes() as $attribute) {
            $attributeArguments = self::generateAttributeArguments($attribute->getArguments());

			$attributeTemplate = trim(str_replace(
				['#attribute#', '#arguments#'],
				[$attribute->getName(), $attributeArguments],
				self::$attributeTemplate
			));

			$attributes[] = $attributeTemplate;
		}

        $template = str_replace(
            ['#property#', '#attributes#'],
            [$property->getName(), implode("\n    ", $attributes)],
            self::$propertyTemplate
        );

        return trim($template);
    }

    private static function generateAttributeArguments(array $arguments): string
    {
        $attributeArguments = [];

        foreach ($arguments as $argumentKey => $argument) {
            $argumentValue = self::getArgumentValue($argument);
            if (DataHandler::isNumeric($argumentKey)) {
                $attributeArguments[] = $argumentValue;
                continue;
            }

            if ($argumentKey === 'targetEntity') {
                $argumentValue = DataHandler::getReplaceString('\'', '', $argumentValue);

                $argumentValue = "'\Smug\Core\Entity\Generated\\" . DataHandler::getReplaceString('\\', '', $argumentValue) . '\'';
            }
            $attributeArguments[] = $argumentKey . ': ' . $argumentValue;
        }

        return implode(",\n    ", $attributeArguments);
    }

    private static function getArgumentValue($argument): string {
        if (DataHandler::isArray($argument)) {
            return self::getArrayArgumentString($argument);
        }

        if (DataHandler::isBool($argument)) {
            return ($argument) ? 'true' : 'false';
        }

        return "'" . $argument . "'";
    }

    private static function getArrayArgumentString(array $argument): string
    {
        $attributeArguments = [];

        foreach ($argument as $subArgumentKey => $subArgument) {
            $subArgumentValue = self::getArgumentValue($subArgument);
            if (DataHandler::isNumeric($subArgumentKey)) {
                $attributeArguments[] = $subArgumentValue;
                continue;
            }
            $attributeArguments[] = "'" . $subArgumentKey . "' => " . $subArgumentValue;
        }

        return "[\n    " . implode(",\n    ", $attributeArguments) . "\n]";
    }
}
