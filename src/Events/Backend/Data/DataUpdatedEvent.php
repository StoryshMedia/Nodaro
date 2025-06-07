<?php
namespace Smug\Core\Events\Backend\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class DataUpdatedEvent extends Event
{
    public const NAME = 'data.updated.event';

    protected BaseModel $data;
    protected string $class;
    protected Context $context;
 
    public function __construct(BaseModel $data, Context $context, string $class)
    {
        $this->data = $data;
        $this->context = $context;
        $this->class = $class;
    }

    public function getData(): BaseModel
    {
        return $this->data;
    }

    public function setData(BaseModel $data): void
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