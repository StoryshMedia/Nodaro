<?php

namespace Smug\FrontendBundle\Subscriber\Backend\View;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\Context\Context;
use Smug\Core\Events\Backend\View\DetailViewCreatedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\FrontendBundle\Constants\Views\Backend\DomainConstants;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Module\Module;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DomainDetailViewSubscriber implements EventSubscriberInterface
{
    protected Context $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DETAIL_VIEW_CREATED => 'onCreateDetailView'
        ];
    }

    public function onCreateDetailView(DetailViewCreatedEvent $event): void
    {
        $data = $event->getViewData();

        if ($event->getClass() === DomainConstants::class) {
            $config = $data->getConfig();
            $config['additionalJavascripts'] = $this->getContentItemJavaScripts();

            $this->context->buildFromData([], Domain::class, []);
            $data->setConfig($config);
        }
        
        $event->setViewData($data);
    }

    public function getContentItemJavaScripts(): array
    {
        $this->context->addRepository('modules', Module::class);
        $items = $this->context->getByRestrictions([['condition' => 'active', 'value' => true]], 'modules');

        $javaScripts = [];

        foreach ($items as $item) {
            $javaScripts = DataHandler::mergeArray(
                $javaScripts,
                DataHandler::getJsonDecode($item->__get('scripts'), true)
            );
        }

        return $javaScripts;
    }
}