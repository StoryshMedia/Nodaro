<?php

namespace Smug\SearchBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Smug\SearchBundle\DependencyInjection\SmugSearchExtension;

class SmugSearchBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SmugSearchExtension();
    }
}
