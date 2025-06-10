<?php

namespace Smug\FrontendBundle\Service\Frontend\Renderer;

use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SiteRenderer
{
    protected FrontendModuleRenderer $renderer;

    public function __construct(
        FrontendModuleRenderer $renderer,
        protected Context $context,
        protected EventDispatcherInterface $dispatcher
    ) {
        $this->renderer = $renderer;
    }

    public function render(array $site): array
    {
        $site['contentItems'] = SiteContentBuilder::getContentItems($site, [], $this->dispatcher, $this->context);
        $site['contentItems'] = $this->renderChildren($site['contentItems']);

        return $site;
    }

    protected function renderContentItem(array $contentItem): array
    {
        $contentItem = $this->renderer->render($contentItem);
        $contentItem['children'] = $this->renderChildren($contentItem['children'] ?? []);

        return $contentItem;
    }

    protected function renderChildren(array $items): array
    {
        foreach ($items as $contentItemKey => $contentItem) {
            if (!DataHandler::doesKeyExists('module', $contentItem)) {
                foreach ($contentItem as $columnKey => $colunmItem) {
                    $items[$contentItemKey][$columnKey] = $this->renderContentItem($colunmItem);
                }
            } else {
                $items[$contentItemKey] = $this->renderContentItem($contentItem);
            }
        }

        return $items;
    }
}
