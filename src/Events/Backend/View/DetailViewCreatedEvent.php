<?php
namespace Smug\Core\Events\Backend\View;

use Smug\AdministrationBundle\Service\Components\Factories\View\View;
use Symfony\Contracts\EventDispatcher\Event;
 
class DetailViewCreatedEvent extends Event
{
    public const NAME = 'detail.view.created.event';

    protected View $viewData;
    protected string $class;
 
    public function __construct(View $viewData, string $class)
    {
        $this->viewData = $viewData;
        $this->class = $class;
    }

    public function getViewData(): View
    {
        return $this->viewData;
    }

    public function setViewData(View $viewData): void
    {
        $this->viewData = $viewData;
    }

    public function getClass(): string
    {
        return $this->class;    
    }
}