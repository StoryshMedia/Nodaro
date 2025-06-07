<?php

namespace Smug\AdministrationBundle\Interface\View\Items;
use Doctrine\Common\Collections\ArrayCollection;

interface RowInterface {
    public function getClass(): string;
    
    public function setClass(string $headline): void;

    public function getFields(): ArrayCollection;

    public function addField(FieldInterface $row): void;
}