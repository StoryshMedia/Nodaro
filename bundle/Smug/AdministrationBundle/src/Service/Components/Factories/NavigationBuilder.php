<?php

namespace Smug\AdministrationBundle\Service\Components\Factories;

use Smug\AdministrationBundle\Interface\Navigation\NavigationBuilderInterface;
use Smug\Core\Security\SecurityProvider;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class NavigationBuilder implements NavigationBuilderInterface {
    public static function collect($userGroup): array {
        return self::transform(
            DataHandler::mergeArray(
                FileContentProvider::getSystemFileContent('navigation.json'),
                FileContentProvider::getSystemFileContent('navigation.json', true)
            ), $userGroup
        );
    }

    public static function transform(array $configurations, $userGroup): array {
        $navigationItems = [];

        foreach ($configurations as $configuration) {
            $navigationItem = new NavigationItem();

            if ($userGroup->__get('admin') === true) {
                $navigationItems[] = $navigationItem->fromArray($configuration); 
                continue;
            }

            if (!DataHandler::doesKeyExists('class', $configuration)) {
                $navigationItems[] = $navigationItem->fromArray($configuration);
                continue;
            }

            $permission = SecurityProvider::getModelPermissionsFromClass($configuration['class'], $userGroup);

            if ($permission === null) {
                $navigationItems[] = $navigationItem->fromArray($configuration);
                continue;
            }
            if ($permission->__get('canRead') === true) {
                $navigationItems[] = $navigationItem->fromArray($configuration); 
            }
        }

        $serializer = new Serializer([new ObjectNormalizer()]);
        $navigation = $serializer->normalize(self::getEntryTree($navigationItems));

        foreach ($navigation as $navigationKey => $item) {
            foreach ($item['children'] as $childKey => $child) {
                if (!DataHandler::searchInMultiArray($child, 'type', 2)) {
                    $item['children'] = DataHandler::unsetArrayElement($item['children'], $childKey);
                }    
            }

            if (DataHandler::getArrayLength($item['children']) === 0) {
                $navigation = DataHandler::unsetArrayElement($navigation, $navigationKey);
                continue;
            }
            $navigation[$navigationKey] = $item;
        }

        return $navigation;
    }

    private static function getEntryTree($entries, string $parent = ''): array
    {
        $branch = [];
        
        /** @var NavigationItem $entry */
        foreach ($entries as $entry) {
            if ($entry->getParent() == $parent) {            
                $children = self::getEntryTree($entries, $entry->getKey());

                if (DataHandler::getArrayLength($children) > 0) {
                    /** @var NavigationItem $child */
                    foreach ($children as $child) {
                        $entry->setChild($child);
                    }
                }
                
                $branch[] = $entry;
            }
        }

        return $branch;
    }
}
