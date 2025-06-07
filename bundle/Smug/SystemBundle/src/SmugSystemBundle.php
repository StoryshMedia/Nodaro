<?php

namespace Smug\SystemBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Smug\SystemBundle\DependencyInjection\SmugSystemExtension;

class SmugSystemBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SmugSystemExtension();
    }
}
