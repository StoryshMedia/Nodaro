<?php

namespace Smug\Core\Service\Base\Components\Serializer;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Serializer\SerializerInterface;

class EntitySerializer
{
    public function __construct(protected SerializerInterface $serializer) {}

    public function serialize(object $entity, array $groups = ['public']): array
    {
        return DataHandler::getJsonDecode(
            $this->serializer->serialize(
                $entity,
                'json',
                [
                    'groups' => $groups,
                    'circular_reference_handler' => function ($object) {
                        return ['id' => $object->getId()];
                    }
                ]
            ),
            true
        );
    }
}
