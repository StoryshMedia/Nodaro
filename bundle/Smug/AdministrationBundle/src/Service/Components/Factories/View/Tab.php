<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View;

use Doctrine\Common\Collections\ArrayCollection;
use Smug\AdministrationBundle\Interface\View\Items\RowInterface;
use Smug\AdministrationBundle\Interface\View\Items\TabInterface;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class Tab implements TabInterface
{
    public function __construct()
    {
        $this->rows = new ArrayCollection();    
    }

    private string $headline;

    private string $icon;
    
    private ArrayCollection $rows;

    public function getHeadline(): string {
        return $this->headline;
    }

    public function setHeadline(string $headline): void {
        $this->headline = $headline;
    }

    public function getIcon(): string {
        return $this->icon;
    }

    public function setIcon(string $icon): void {
        $this->icon = $icon;
    }

    public function getRows(): ArrayCollection {
        return $this->rows;        
    }

    public function addRow(RowInterface $row): void {
        if (!$this->rows->contains($row)) {
            $this->rows->add($row);
        }
    }

    public function fromArray(array $data): Tab {
        $this->setHeadline($data['headline'] ?? '');

        if (DataHandler::doesKeyExists('rows', $data)) {
            foreach ($data['rows'] as $row) {
                if (DataHandler::isInstanceOf($row, RowInterface::class)) {
                    $this->addRow($row);
                }
            }
        }

        return $this;
    }

    public function toArray(): array {
        return [
            'rows' => ArrayProvider::getObjectsAsArray($this->getRows()),
            'icon' => $this->getIcon(),
            'headline' => $this->getHeadline()
        ];
    }
}
