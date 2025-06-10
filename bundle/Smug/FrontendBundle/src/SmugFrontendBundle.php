<?php

namespace Smug\FrontendBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Smug\FrontendBundle\DependencyInjection\SmugFrontendExtension;

class SmugFrontendBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SmugFrontendExtension();
    }
}
