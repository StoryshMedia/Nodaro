<?php
namespace Smug\Core\Events\Backend\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class DataConstantsLoadedEvent extends Event
{
    public const NAME = 'data.constants.loaded.event';

    protected BaseModel|array $data;
    protected string $class;
    protected Context $context;
 
    public function __construct(BaseModel|array $data, Context $context, string $class)
    {
        $this->data = $data;
        $this->context = $context;
        $this->class = $class;
    }

    public function getData(): BaseModel|array
    {
        return $this->data;
    }

    public function setData(BaseModel|array $data): void
    {
        $this->data = $data;
    }

    public function getClass(): string
    {
        return $this->class;    
    }

    public function getContext(): Context
    {
        return $this->context;    
    }
}