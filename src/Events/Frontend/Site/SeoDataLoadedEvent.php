<?php
namespace Smug\Core\Events\Frontend\Site;

use Symfony\Contracts\EventDispatcher\Event;
 
class SeoDataLoadedEvent extends Event
{
    public const NAME = 'plugin.seo.data.loaded.event';

    protected array $data;
 
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }
}