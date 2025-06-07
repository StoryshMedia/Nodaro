<?php

namespace Smug\Core\Hydrator\Base;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use \Exception;
use ReflectionClass;
use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Hydrator\Base\Field\Field;

class BaseHydrator
{
    public static function hydrateArray(array $data, Context $context): BaseModel
    {
		$class = EntityGenerator::getGeneratedEntity($context->getMainRepositoryClass());

		$disabledFields = [];
		$hiddenFields = [];

		if ($context->getMode() === 'be') {
			$disabledFields = $context->getSpecialFields($class, 'disabledFields');
			$hiddenFields = $context->getSpecialFields($class, 'hiddenFields');
		}
		
		$rc = new ReflectionClass($class);
		
		if (DataHandler::doesKeyExists('id', $data)) {
			$return = $context->getMainRepository()->findOneBy(['id' => $data['id']]);
		} else {
			$return = new $class();
		}

		foreach ($rc->getProperties() as $mapping) {
			if (!DataHandler::doesKeyExists('id', $data) && $mapping->getName() === 'id') {
				continue;
			}

			if (DataHandler::isInArray($mapping->getName(), $disabledFields) || DataHandler::isInArray($mapping->getName(), $hiddenFields)) {
				continue;
			}

			if (!DataHandler::doesKeyExists($mapping->getName(), $data)) {
				$defaultValue = $mapping->getAttributes(DefaultValue::class);
				if (!DataHandler::isEmpty($defaultValue)) {
					$data[$mapping->getName()] = $defaultValue[0]->getArguments()[0];
				} else {
					continue;
				}
			}
			
			$field = Field::buildFromType($mapping);
			
			if (DataHandler::isEmpty($field) ) {
				continue;
			}

			$value = $field::hydrate($data, $mapping->getName(), $mapping->getAttributes());

			$return->__set($mapping->getName(), $value);
		}
	    try {
	    } catch (Exception $exception) {
        	ExceptionProvider::getException($exception);
	    }
		
        return $return;
    }
}
