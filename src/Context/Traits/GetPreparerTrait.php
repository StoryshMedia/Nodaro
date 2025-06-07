<?php

declare(strict_types=1);

namespace Smug\Core\Context\Traits;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

trait GetPreparerTrait
{
    /**
     * @return string|null
     */
    public function getPreparer(string $repositoryClass): ?string
    {
        if (DataHandler::doesClassExist($repositoryClass . 'Preparer')) {
            return $repositoryClass . 'Preparer';
        }

        return null;
    }
}
