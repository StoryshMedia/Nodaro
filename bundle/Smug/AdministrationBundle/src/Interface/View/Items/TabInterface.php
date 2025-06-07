<?php

namespace Smug\AdministrationBundle\Interface\View\Items;
use Doctrine\Common\Collections\ArrayCollection;

interface TabInterface {
    public function getHeadline(): string;
    
    public function setHeadline(string $headline): void;

    public function getRows(): ArrayCollection;

    public function addRow(RowInterface $row): void;
}