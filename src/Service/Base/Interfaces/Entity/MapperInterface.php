<?php

namespace Smug\Core\Service\Base\Interfaces\Entity;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;

interface MapperInterface
{
	public static function handleMappingStates(BaseModel $entity, array $data, Context $context, array $config = []): array;
}
