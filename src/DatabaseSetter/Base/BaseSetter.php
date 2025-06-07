<?php

namespace Smug\Core\DatabaseSetter\Base;

use Doctrine\DBAL\ParameterType;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Struct\Base\StructInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Smug\Core\DataAbstractionLayer\EntityGenerator;

class BaseSetter implements SetterInterface
{
    /** @var EntityManagerInterface $em */
    protected EntityManagerInterface $em;
    
	/**
	 * BaseSetter constructor.
	 * @param EntityManagerInterface $em
	 */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
	
	/**
	 * @inheritDoc
	 */
    public function performCreate(StructInterface $struct, string $class, array $mappings): BaseModel
    {
	    $newEntry = new $class();
	
	    foreach ($mappings as $mapping) {
	    	$getterFunction = 'get' . DataHandler::getFirstCapitalUpper($mapping);
	    	$setterFunction = 'set' . DataHandler::getFirstCapitalUpper($mapping);
		    $newEntry->$setterFunction($struct->$getterFunction());
	    }
	    
	    $this->em->persist($newEntry);
	    $this->em->flush();
	
	    return $newEntry;
    }

	/**
	 * @inheritDoc
	 */
    public function performUpdate(StructInterface $struct, string $class, array $mappings, BaseModel $model = null): ?BaseModel
    {
    	if ($model === null) {
		    /** @var BaseModel $model */
		    $model = $this->em->getRepository($class)->findOneBy(['id' => $struct->__get('id')]);

			if (DataHandler::isEmpty($model)) {
				throw new Exception('No data found for identifier "' . $struct->__get('id') . '"');
			}

		    $queryBuilder = $this->em->createQueryBuilder();
		
		    $queryBuilder->update($class, 'c');
		
		    foreach ($mappings as $mapping) {
			    $queryBuilder->set('c.' . $mapping, ':' . $mapping);
		    }
		
		    $queryBuilder->where('c.id = :entryId')
			    ->setParameter('entryId', $struct->__get('id'));
		
		    foreach ($mappings as $mapping) {
			    $getterFunction = 'get' . DataHandler::getFirstCapitalUpper($mapping);
			
			    $queryBuilder->setParameter($mapping, $struct->$getterFunction());
		    }

		    $queryBuilder->getQuery()
			    ->execute();

			$builder = $this->em->createQueryBuilder();
			$result = $builder->select('c')
				->from($class, 'c')
				->where('c.id = :entityId')
				->setParameter('entityId', $struct->__get('id'), ParameterType::STRING)
				->getQuery()->getOneOrNullResult();

			$this->em->refresh($result);
		    return $result;
	    } else {
		    foreach ($mappings as $mapping) {
			    $getterFunction = 'get' . DataHandler::getFirstCapitalUpper($mapping);
			    $setterFunction = 'set' . DataHandler::getFirstCapitalUpper($mapping);
			    $model->$setterFunction($struct->$getterFunction());
		    }
		
		    $this->em->persist($model);
		    $this->em->flush();
		
		    return $model;
	    }
    }
	
	/**
	 * @param array $data
	 * @param string $class
	 * @return BaseModel
	 */
	public function createAssociationModel(
		array $data
	): BaseModel
	{
		$queryBuilder = $this->em->getConnection()->createQueryBuilder();

        $queryBuilder->insert($data['config']['table'])
			->values([$data['config']['base'] => '?', $data['config']['association'] => '?'])
			->setParameters([0 => $data['base']->getId(), 1 => $data['association']->getId()]);

		$queryBuilder->execute();

	    return $data['base'];
	}
	
	/**
	 * @param array $data
	 * @param string $class
	 * @return BaseModel
	 */
	public function removeAssociationModel(
		array $data
	): BaseModel
	{
		$queryBuilder = $this->em->getConnection()->createQueryBuilder();

        $queryBuilder->delete($data['config']['table'])
			->where($data['config']['base'] . ' = ?')
			->andWhere($data['config']['association'] . ' = ?')
			->setParameters([0 => $data['base']->getId(), 1 => $data['association']->getId()])
            ->execute();

	    return $data['base'];
	}
	
	/**
	 * @param $attachment
	 * @param array $data
	 * @param string $class
	 * @return BaseModel
	 */
	public function setMediaAsAttachment($attachment, array $data, string $class): BaseModel
	{
		$media = $attachment;
		
		if (DataHandler::isArray($attachment)) {
			$data['media'] = $this->setMedia($attachment);
		}
		
		$data['media'] = $media;
		
		return $this->createAssociationModel($data, $class);
	}
	
	/**
	 * @param array $data
	 * @return BaseModel
	 */
	public function setMedia(array $data): BaseModel
	{
		$class = EntityGenerator::getGeneratedEntity(Media::class);
		$media = new $class();
		
		$media->__set('extension', $data['extension']);
		$media->__set('path', $data['path']);
		$media->__set('file', DataHandler::getReplaceString(' ', '_', $data['file']));
		$media->__set('size', $data['size']);
		$media->__set('sizeX', $data['sizeX']);
		$media->__set('sizeY', $data['sizeY']);
		$media->__set('type', 'IMAGE');
		$media->__set('optimized', false);

		$this->em->persist($media);
		$this->em->flush();
		
		return $media;
	}
	
	/**
	 * @inheritDoc
	 */
    public function createAssociation(BaseModel $baseModel, BaseModel $association, string $setterFunction): BaseModel
    {
	    $baseModel->$setterFunction($association);
	
	    $this->em->persist($baseModel);
	    $this->em->flush();
	
	    return $baseModel;
    }
	
	/**
	 * @inheritDoc
	 */
    public function removeAssociation(BaseModel $baseModel, string $setterFunction, BaseModel $association = null): BaseModel
    {
	    $baseModel->$setterFunction($association);
	
	    $this->em->persist($baseModel);
	    $this->em->flush();
	
	    return $baseModel;
    }
	
	/**
	 * @inheritDoc
	 */
	public function performRemove(BaseModel $model): bool
	{
		$this->em->remove($model);
		$this->em->flush();
		
		return true;
	}
}
