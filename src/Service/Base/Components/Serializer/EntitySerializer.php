<?php

namespace Smug\Core\Service\Base\Components\Serializer;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\Serializer\SerializerInterface;

class EntitySerializer
{
    public function __construct(protected SerializerInterface $serializer) {}

    public function serialize(object $object, array $groups = ['public']): array
    {
        $data = DataHandler::getJsonDecode(
            $this->serializer->serialize(
                $object,
                'json',
                [
                    'groups' => $groups,
                    'circular_reference_handler' => fn ($object) => ['id' => $object->__get('id')],
                ]
            ),
            true
        );

        foreach ($data as $key => $value) {
            if (
                is_array($value) &&
                isset($value[0]['parentId']) &&
                isset($value[0]['id'])
            ) {
                $data[$key] = $this->buildTree($value);
            }
        }

        return $data;
    }

    private function buildTree(array $elements, $parentId = ""): array
    {
        $branch = [];

        foreach ($elements as $element) {
            if (($element['parentId'] ?? "") === $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
