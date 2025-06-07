<?php

namespace Smug\Core\Service\Base\Components\Processor;

use Smug\Core\DatabaseSetter\Base\BaseSetter;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Interfaces\Provider\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use \Exception;

abstract class AssociationModelProcessor implements ProviderInterface
{
    public static function process(
        EntityManagerInterface $em,
        BaseSetter $setter,
        array $data
    ): array {

        $em->beginTransaction();

        try {
            //update the entity
            $association = $setter->createAssociationModel($data);

            $em->commit();

        } catch (Exception $exception) {
            $em->rollback();

            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true,
            'data' => $association
        ];
    }
}
