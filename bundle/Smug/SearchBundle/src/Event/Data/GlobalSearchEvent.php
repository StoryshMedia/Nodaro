<?php
namespace Smug\SearchBundle\Event\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class GlobalSearchEvent extends Event
{
    public const NAME = 'data.global.search.perform';

    protected BaseModel|array $data;
    protected array $config;
    protected Context $context;
 
    public function __construct(
        BaseModel|array $data,
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