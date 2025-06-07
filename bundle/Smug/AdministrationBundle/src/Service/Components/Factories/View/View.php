<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\View\Items\TabInterface;
use Smug\AdministrationBundle\Interface\View\ViewInterface;
use Smug\Core\Context\Context;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class View implements ViewInterface
{
    public function __construct()
    {
        $this->tabs = new ArrayCollection();    
    }

    private array $config;

    private Context $context;

    private ArrayCollection $tabs;

    public function getContext(): Context {
        return $this->context;
    }

    public function setContext(Context $context): void {
        $this->context = $context;
    }

    public function getConfig(): array {
        return $this->config;
    }

    public function setConfig(array $config): void {
        $this->config = $config;
    }

    public function addConfigItem(string $key, array|bool|string|int $item): void {
        $this->config[$key] = $item;
    }

    public function getTabs(): ArrayCollection {
        return $this->tabs;        
    }

    public function getTab(string $headline): ?TabInterface {
        foreach ($this->tabs as $tab) {
            if ($tab->getHeadline() === $headline) {
                return $tab;
            }
        }

        return null;        
    }

    public function addTab(TabInterface $tab): void {
        if (!$this->tabs->contains($tab)) {
            $this->tabs->add($tab);
        }
    }

    public function fromArray(array $data): View {
        $this->setConfig($data['config']);

        if (DataHandler::doesKeyExists('tabs', $data)) {
            foreach ($data['tabs'] as $tab) {
                if (DataHandler::isInstanceOf($tab, TabInterface::class)) {
                    $this->addTab($tab);
                }
            }
        }

        return $this;
    }

    public function toArray(): array {
        return [
            'tabs' => ArrayProvider::getObjectsAsArray($this->getTabs()),
            'config' => $this->getConfig()
        ];
    }
}
