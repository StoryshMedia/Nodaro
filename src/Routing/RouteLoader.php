<?php

namespace Smug\Core\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Finder\Finder;
use \Exception;

class RouteLoader extends Loader
{
    public function load($resource, ?string $type = null): RouteCollection
    {
        $routes = new RouteCollection();
        $finder = new Finder();

        $finder
            ->directories()->in(__DIR__. '/../../bundle')->depth(1);
        
        foreach ($finder as $dir) {
            if (!\str_contains($dir->getFilename(), 'Bundle')) {
                continue;
            }
            
            $resource = '@' . $dir->getRelativePath() . $dir->getFilename() . '/config/routes.yaml';
            $type = 'yaml';
    
            try {
                $importedRoutes = $this->import($resource, $type);
        
                $routes->addCollection($importedRoutes);
            } catch (Exception $e) {
                continue;
            }
        }

        return $routes;
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'advanced_extra' === $type;
    }
}