<?php
namespace Smug\Core\Events\Frontend\Site;

use Smug\Core\Context\Context;
use Symfony\Contracts\EventDispatcher\Event;
 
class ContentItemLoadedEvent extends Event
{
    public const NAME = 'content.item.loaded.event';

    protected array $data;

    protected ?Context $context = null;
 
    public function __construct(array $data, ?Context $context = null)
    {
        $this->data = $data;
        $this->context = $context;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getContext(): ?Context
    {
        return $this->context;
    }

    public function setContext(?Context $context = null): void
    {
        $this->context = $context;
    }
}