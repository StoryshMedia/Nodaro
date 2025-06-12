<?php

namespace Smug\Core\Service\Base\Factory\Finder;

use Symfony\Component\Finder\Finder;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class FinderFactory
{
    public static function getElements(string $path, int $depth = -1, bool $files = true, array $name = []): array|Finder
    {
        if (!DataHandler::proofDir($path)) {
            return [];
        }

        $finder = new Finder();

        try {
            if ($files) {
                $finder->files();
            } else {
                $finder->directories();
            }

            $finder->in($path);

            if ($depth >= 0) {
                $finder->depth($depth);
            }

            if (!DataHandler::isEmpty($name)) {
                $finder->name($name);
            }

            return $finder;
        } catch (\Throwable) {
            return [];
        }
    }
}
