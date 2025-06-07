<?php
namespace Smug\Core\Events\Backend\Data;

use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class EntityFieldLoadedEvent extends Event
{
    public const NAME = 'data.entity.field.loaded.event';

    protected mixed $data;
    protected string $class;
    protected string $field;
 
    public function __construct(mixed $data, string $class, string $field)
    {
        $this->data = $data;
        $this->class = $class;
        $this->field = $field;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    public function getClass(): string
    {
        return $this->class;    
    }

    public function getField(): string
    {
        return $this->field;    
    }
}