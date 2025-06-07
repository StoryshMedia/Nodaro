<?php

namespace Smug\Core\Service\Base\Components\Processor;

use Smug\Core\DatabaseSetter\Base\BaseSetter;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Struct\Base\StructInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class RemoveProcessor implements ProcessorInterface
{
    public static function process(
        EntityManagerInterface $em,
        BaseSetter $setter,
        ?StructInterface $data = null,
        string $class = '',
        array $mappings = [],
        bool $returnObject = false,
        ?BaseModel $model = null
    ) {
        $em->beginTransaction();

        try {
            $setter->performRemove($model);

            $em->commit();

        } catch (\Exception $exception) {
            $em->rollback();

            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }
}
