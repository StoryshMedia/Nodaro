<?php

namespace Smug\Core\DatabaseSetter\Base;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Struct\Base\StructInterface;
use Doctrine\ORM\NonUniqueResultException;

interface SetterInterface
{
	/**
	 * @param StructInterface $struct
	 * @param string $class
	 * @param array $mappings
	 * @return BaseModel
	 * @throws NonUniqueResultException
	 */
	public function performCreate(StructInterface $struct, string $class, array $mappings): BaseModel;
	
	/**
	 * @param StructInterface $struct
	 * @param string $class
	 * @param array $mappings
	 * @param BaseModel|null $model
	 * @return mixed
	 */
	public function performUpdate(StructInterface $struct, string $class, array $mappings, BaseModel $model = null): ?BaseModel;
	
	/**
	 * @param BaseModel $baseModel
	 * @param BaseModel $association
	 * @param string $setterFunction
	 * @return BaseModel
	 */
	public function createAssociation(BaseModel $baseModel, BaseModel $association, string $setterFunction): BaseModel;
	
	/**
	 * @param BaseModel $baseModel
	 * @param string $setterFunction
	 * @param BaseModel|null $association
	 * @return BaseModel
	 */
	public function removeAssociation(BaseModel $baseModel, string $setterFunction, BaseModel $association = null): BaseModel;
	
	/**
	 * @param BaseModel $model
	 * @return bool
	 */
	public function performRemove(BaseModel $model): bool;
}
