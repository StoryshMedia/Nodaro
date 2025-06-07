<?php

namespace Smug\Core\Service\Base\Components\Factory\Entity;

use ReflectionClass;
use ReflectionProperty;
use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Interfaces\Entity\MapperInterface;

class AssociationMapperFactory implements MapperInterface
{
    /**
     * @inheritdoc
     */
	public static function handleMappingStates(BaseModel $entity, array $data, Context $context, array $config = []): array
    {
        $existingAssociations = self::getExistingAssociations($entity, $context, $config);

        if ($config['multiple'] === false) {
            self::removeAllExisting($entity, $existingAssociations, $data, $context, $config);
        } else {
            self::removeDeleted($entity, $existingAssociations, $data, $context, $config);
        }
        
        return self::setDataAssociations($entity, $data, $context, $config);
    }

    protected static function setDataAssociations(BaseModel $entity, array $data, Context $context, array $config = []) :array
    {
        $dataAssociations = [];

        foreach ($data as $association) {
            $id = $association['id'] ?? self::getAssociationIdentifier($association, $config);

            $associationObject = $context->getEntityByIdentifier(
                $id,
                'id',
                $config['associationIdentifier']
            );

            if (!DataHandler::isEmpty($associationObject)) {
                $dataAssociations[] = $associationObject;
                continue;
            }

            $model = new $config['associationClass']();
            $rc = new ReflectionClass($model);
    
            $model = self::setOwningSide($model, $rc, $entity);
            $model = self::setAssociationSide($model, $rc, $association, $context);
            $context->getEntityManager()->persist($model);
            $context->getEntityManager()->flush();
            
            $entity->__add($config['property'], $model);
            $context->getEntityManager()->persist($entity);
            $context->getEntityManager()->flush();

            $dataAssociations[] = $model;
        }

        return $dataAssociations;
    }

    protected static function getAssociationIdentifier(array $association, array $config): string
    {
        return $association[$config['associationBaseIdentifier']]['id'];
    }

    protected static function setOwningSide(BaseModel $newAssociation, ReflectionClass $class, BaseModel $owning): BaseModel
    {
        foreach ($class->getProperties() as $property) {
            $targetEntity = self::getTargetEntity($property);

            if (DataHandler::isEmpty($targetEntity)) {
                continue;
            }

            if (DataHandler::isInstanceOf($owning, $targetEntity)) {
                $newAssociation->__set($property->getName(), $owning);
            }
        }

        return $newAssociation;
    }

    protected static function setAssociationSide(BaseModel $newAssociation, ReflectionClass $class, array $data, Context $context): BaseModel
    {
        foreach ($class->getProperties() as $property) {
            $targetEntity = self::getTargetEntity($property);

            if (DataHandler::isEmpty($targetEntity)) {
                continue;
            }

            if (DataHandler::doesKeyExists($property->getName(), $data) && DataHandler::doesKeyExists($property->getName(), $context->getRepositories())) {
                $newAssociation->__set(
                    $property->getName(),
                    $context->getEntityByIdentifier($data[$property->getName()]['id'], 'id', $property->getName())
                );
            }
        }

        return $newAssociation;
    }

    protected static function getExistingAssociations(BaseModel $entity, Context $context, array $config = []): array
    {
        $builder = $context->getEntityManager()->createQueryBuilder();
        return $builder->select('c')
          ->from($config['associationClass'], 'c')
          ->where('c.field = :entity')
          ->setParameter('entity', $entity)
          ->getQuery()
          ->getResult();
    }

    private static function removeAllExisting(BaseModel $entity, array $associations, array $data, Context $context, array $config): void
    {
        foreach ($associations as $existingAssociation) {
            $stillExsisting = false;

            foreach ($data as $association) {
                $id = $association['id'] ?? '';
                if ($existingAssociation->getId() === $id) {
                    $stillExsisting = true;
                    break;
                }
            }

            if ($stillExsisting === false) {
                self::removeExisting($entity, $existingAssociation, $context, $config);
            }
        }

        $context->getEntityManager()->flush();
    }

    private static function removeDeleted(BaseModel $entity, array $existing, array $data, Context $context, array $config): void
    {
        foreach ($existing as $existingAssociation) {
            $stillExsisting = false;

            foreach ($data as $association) {
                if ($existingAssociation->getId() === $association['id']) {
                    $stillExsisting = true;
                    break;
                }
            }

            if ($stillExsisting === false) {
                self::removeExisting($entity, $existingAssociation, $context, $config);
            }
        }

        $context->getEntityManager()->flush();
    }

    private static function removeExisting(BaseModel $entity, BaseModel $association, Context $context, array $config): void
    {
        $entity->__remove($config['property'], $association);
        $context->getEntityManager()->remove($association);
    }

    protected static function getTargetEntity(ReflectionProperty $property): string
    {
        foreach ($property->getAttributes() as $attribute) {
            if (!DataHandler::doesKeyExists('targetEntity', $attribute->getArguments())) {
                continue;
            }
            
            return $attribute->getArguments()['targetEntity'];
        }

        return '';
    }
}
