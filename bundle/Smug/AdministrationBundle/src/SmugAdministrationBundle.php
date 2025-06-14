<?php

namespace Smug\AdministrationBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Smug\AdministrationBundle\DependencyInjection\SmugAdministrationExtension;

class SmugAdministrationBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SmugAdministrationExtension();
    }
}
