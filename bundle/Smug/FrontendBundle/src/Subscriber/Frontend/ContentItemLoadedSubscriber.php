<?php

namespace Smug\FrontendBundle\Subscriber\Frontend;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\Restriction\RestrictionBuilder;
use Smug\Core\Events\Frontend\Site\ContentItemLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\FrontendBundle\Entity\Site\Site;
use Smug\FrontendBundle\Event\FrontendEvents;
use Smug\FrontendBundle\Service\Factory\FrontendNavigationFactory;

class ContentItemLoadedSubscriber extends ContentItemRenderingSubscriber
{
    protected array $identifiers;

    protected Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->identifiers = [
            'SmugInternLinkTile',
            'SmugSimpleNavigation',
            'SmugItemTeaser'
        ];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FrontendEvents::FRONTEND_CONTENT_ITEM_LOADED => 'onContentItemLoaded'
        ];
    }

    public function onContentItemLoaded(ContentItemLoadedEvent $event): void
    {
        $data = $event->getData();

        if (self::doProcess($data, $this->identifiers[0])) {
            if (DataHandler::isArray($data['variables']['pluginSettings']['item'] ?? [])) {
                $data['variables']['entry'] = $data['variables']['pluginSettings']['item'];
            } else {
                $data['variables']['entry'] = DataHandler::getJsonDecode($data['variables']['pluginSettings']['item'] ?? [], true);
            }
        }

        if (self::doProcess($data, $this->identifiers[1])) {
            $frontendNavigationFactory = new FrontendNavigationFactory();
            $siteSelectValue = self::getSiteSelectValue($data['module']['fields']);
            if (!DataHandler::isEmpty($siteSelectValue)) {
                $site = $this->context->getEntityManager()->getRepository(EntityGenerator::getGeneratedEntity(Site::class))->findOneBy(['id' => $siteSelectValue['id'] ?? '']);
                $restrictions = $this->getNavigationRestrictions();

                $siteTree = DataHandler::getTree(
                    ArrayProvider::getObjectsAsArray(
                        $site->__get('domain')->__get('sites'),
                        ['contentItems'],
                        true,
                        $restrictions
                    )
                );
                $navigation = $frontendNavigationFactory->getNavigationFromSiteTree(
                    $siteTree,
                    $siteSelectValue['id'] ?? ''
                );
                $data['variables']['menu'] = $navigation['children'] ?? [];
            }
        }

        if (self::doProcess($data, $this->identifiers[2])) {
            foreach ($data['variables']['tabs'] as $tabKey => $tab) {
                $itemData = DataHandler::getJsonDecode($tab['item']['value'] ?? '', true);

                if (DataHandler::isEmpty($itemData['section'] ?? '')) {
                    continue;
                }

                $queryBuilder = $event->getContext()->getEntityManager()->createQueryBuilder();
                $queryBuilder->select('item')
                    ->from(EntityGenerator::getGeneratedEntity($itemData['section']), 'item')
                    ->where('item.id = :id')
                    ->setParameter('id', $itemData['item']);

                $data['variables']['tabs'][$tabKey]['data'] = $queryBuilder->getQuery()->getSingleResult()->toArray();
            }
        }

        $event->setData($data);
    }

    protected static function getSiteSelectValue(array $fields): array {
        foreach ($fields as $field) {
            if ($field['identifier'] === 'startingSite') {
                $startingSite = DataHandler::isEmpty($field['value']) ? '[]' : $field['value'];
                return DataHandler::getJsonDecode($startingSite, true);
            }
        }

        return [];
    }

    protected function getNavigationRestrictions(): array
    {
        $restrictions = [];

        $restrictions[] = RestrictionBuilder::build('boolean', 'hidden', false);
        $restrictions[] = RestrictionBuilder::build('boolean', 'hiddenInMenu', false);
        
        return $restrictions;
    }
}