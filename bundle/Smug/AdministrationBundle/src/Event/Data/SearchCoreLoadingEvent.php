<?php
namespace Smug\AdministrationBundle\Event\Data;

use Smug\Core\Context\Context;
use Smug\Core\Entity\Base\BaseModel;
use Symfony\Contracts\EventDispatcher\Event;
 
class SearchCoreLoadingEvent extends Event
{
    public const NAME = 'data.search.core.loading';

    protected BaseModel|array $data;
    protected Context $context;
 
    public function __construct(BaseModel|array $data, Context $context)
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