<?php
namespace Smug\FrontendBundle\Event\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Smug\FrontendBundle\Entity\Module\Module;
use Symfony\Contracts\EventDispatcher\Event;
 
class FilterLoadedEvent extends Event
{
    public const NAME = 'data.search.perform';

    protected BaseModel|array $data;
    protected array $config;
    protected Context $context;
    protected Module $module;
 
    public function __construct(
        BaseModel|array $data,
        Context $context
    ) {
        $this->module = $data['module'];
        $this->data = $data['items'];
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

    public function getModule(): Module 
    {
        return $this->module;
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