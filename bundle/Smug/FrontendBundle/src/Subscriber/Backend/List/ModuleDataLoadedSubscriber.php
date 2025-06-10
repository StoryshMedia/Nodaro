<?php

namespace Smug\FrontendBundle\Subscriber\Backend\List;

use Smug\AdministrationBundle\Event\SystemEvents;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\Core\Events\Backend\Data\DataModelListLoadedEvent;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ModuleDataLoadedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SystemEvents::DATA_MODEL_LIST_LOADED => 'onDataLoaded'
        ];
    }

    public function onDataLoaded(DataModelListLoadedEvent $event): void
    {
        if ($event->getClass() === EntityGenerator::getGeneratedEntity(Module::class)) {
            $data = $event->getData();

            foreach ($data as $moduleKey => $module) {
                $publicModulePath = DIRECTORY_SEPARATOR . 'public/Resources/elements' . DIRECTORY_SEPARATOR . $module['identifier'] . DIRECTORY_SEPARATOR;
                if (DataHandler::doesFileExist($event->getContext()->getKernel()->getProjectDir() . DIRECTORY_SEPARATOR . $publicModulePath . 'images' . DIRECTORY_SEPARATOR . 'preview.webp')) {
                    $data[$moduleKey]['assets']['preview'] = DIRECTORY_SEPARATOR . 'Resources/elements' . DIRECTORY_SEPARATOR . $module['identifier'] . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'preview.webp';
                } else {
                    $data[$moduleKey]['assets']['preview'] = DIRECTORY_SEPARATOR . 'Resources/elements/fallback/images/preview.webp';
                }
            }

            $event->setData($data);
        }
    }
}