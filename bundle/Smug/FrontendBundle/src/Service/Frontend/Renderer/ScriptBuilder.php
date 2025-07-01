<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Serializer\EntitySerializer;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScriptBuilder
{
    protected const MAPPINGS = [
        'headerTop',
        'headerBottom',
        'footerTop',
        'footerBottom',
        'bodyEnd',
        'bodyStart'
    ];

    public static function getSiteScripts(array $data, EntitySerializer $serializer, EventDispatcherInterface $dispatcher = null): array
    {
        $site = $data['site'];
        $siteArray = (DataHandler::isInstanceOf($site, EntityGenerator::getGeneratedEntity(Site::class))) ? $serializer->serialize($site) : $site;
        $siteScripts = [
            'headerTop' => [],
            'headerBottom' => [],
            'footerTop' => [],
            'footerBottom' => [],
            'bodyEnd' => [],
            'bodyStart' => []
        ];

        foreach ($siteArray['siteScripts'] as $siteScript) {
            $siteScript['script']['template'] = DataHandler::getJsonDecode($siteScript['script']['template'], true);
            $siteScripts[self::MAPPINGS[$siteScript['area'] ?? 0]][] = $siteScript;
        }

        return $siteScripts;
    }
}
