<?php

namespace Smug\FrontendBundle\Service\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class FrontendNavigationFactory
{
    public function getNavigationFromSiteTree(array $sites, string $startingPoint): array
    {
        $result = [];

        foreach ($sites as $site) {
            if ($site['id'] === $startingPoint) {
                return $site;
            }
    
            if (!DataHandler::isEmpty($site['children'] ?? [])) {
                $result = $this->getNavigationFromSiteTree($site['children'], $startingPoint);
            }
        }
        
        return $result;
    }
}