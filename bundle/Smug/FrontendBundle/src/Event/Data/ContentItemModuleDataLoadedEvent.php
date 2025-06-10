<?php
namespace Smug\FrontendBundle\Event\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class ContentItemModuleDataLoadedEvent extends Event
{
    public const NAME = 'smug.frontend.bundle.content.item.module.data.loaded';

    protected $data;
    protected array $config;
    protected Context $context;
 
    public function __construct(
        $data,
        Context $context
    ) {
        $this->data = $data;
        $this->context = $context;
    }

    public function getData(): BaseModel|array
    {
        return $this->data;
    }

    public function setData(BaseModel|array $data): void
    {
        $this->data = $data;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getContext(): Context
    {
        return $this->context;    
    }
}