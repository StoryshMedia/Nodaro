<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ScriptBuilder
{
    protected const MAPPINGS = [
        'headerTop',
        'headerBottom',
        'footerTop',
        'footerBottom'
    ];

    public static function getSiteScripts(array $data, EventDispatcherInterface $dispatcher = null): array
    {
        $site = $data['site'];
        $siteArray = (DataHandler::isInstanceOf($site, EntityGenerator::getGeneratedEntity(Site::class))) ? $site->toArray() : $site;
        $siteScripts = [
            'headerTop' => [],
            'headerBottom' => [],
            'footerTop' => [],
            'footerBottom' => []
        ];

        foreach ($siteArray['siteScripts'] as $siteScript) {
            $siteScript['script']['template'] = DataHandler::getJsonDecode($siteScript['script']['template'], true);
            $siteScripts[self::MAPPINGS[$siteScript['area'] ?? 0]][] = $siteScript;
        }

        return $siteScripts;
    }
}
