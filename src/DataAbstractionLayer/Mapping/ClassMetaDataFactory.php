<?php
declare(strict_types=1);

namespace Smug\Core\DataAbstractionLayer\Mapping;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataFactory as BaseClassMetaDataFactory;
use Doctrine\ORM\Mapping\DefaultTypedFieldMapper;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Smug\Core\DataAbstractionLayer\Persistance\RuntimeReflectionService;

class ClassMetaDataFactory extends BaseClassMetaDataFactory
{
    /** @return void */
    public function setEntityManager(EntityManagerInterface $em) {
        parent::setEntityManager($em);
    }

    protected function newClassMetadataInstance($className)
    {
        return new ClassMetaData(
            $className,
            new UnderscoreNamingStrategy(),
            new DefaultTypedFieldMapper()
        );
    }
}