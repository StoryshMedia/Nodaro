<?php
namespace Smug\Core\Events\Email;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class EmailSendEvent extends Event
{
    public const NAME = 'data.send.email';

    protected array $data;
    protected Context $context;
 
    public function __construct(array $data, Context $context)
    {
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
    
    public function getContext(): Context
    {
        return $this->context;    
    }
}