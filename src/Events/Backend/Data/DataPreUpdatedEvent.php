<?php
namespace Smug\Core\Events\Backend\Data;

use Smug\Core\Context\Context;
use Symfony\Contracts\EventDispatcher\Event;
 
class DataPreUpdatedEvent extends Event
{
    public const NAME = 'data.pre.updated.event';

    protected array $data;
    protected string $class;
    protected Context $context;
 
    public function __construct(array $data, Context $context, string $class)
    {
        $this->data = $data;
        $this->context = $context;
        $this->class = $class;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
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