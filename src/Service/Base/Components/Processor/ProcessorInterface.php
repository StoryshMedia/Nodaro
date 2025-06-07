<?php

namespace Smug\Core\Service\Base\Components\Processor;

use Smug\Core\DatabaseSetter\Base\BaseSetter;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Struct\Base\StructInterface;
use Doctrine\ORM\EntityManagerInterface;

interface ProcessorInterface
{
    /**
     * @param EntityManagerInterface $em
     * @param BaseSetter $setter
     * @param StructInterface|null $data
     * @param string $class
     * @param array $mappings
     * @param bool $returnObject
     * @param BaseModel|null $model
     * @return array|BaseModel
     */
    public static function process(
        EntityManagerInterface $em,
        BaseSetter $setter,
        ?StructInterface $data = null,
        string $class = '',
        array $mappings = [],
        bool $returnObject = false,
        ?BaseModel $model = null
    );
}
