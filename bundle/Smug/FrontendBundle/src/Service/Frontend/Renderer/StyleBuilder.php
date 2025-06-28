<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class StyleBuilder
{
    public static function getSiteStyles(array $data, EntitySerializer $serializer, EventDispatcherInterface $dispatcher = null): array
    {
        $site = $data['site'];
        $siteArray = (DataHandler::isInstanceOf($site, EntityGenerator::getGeneratedEntity(Site::class))) ? $serializer->serialize($site) : $site;

        return $siteArray['siteStyles'] ?? [];
    }
}
