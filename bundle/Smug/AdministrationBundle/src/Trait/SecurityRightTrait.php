<?php

namespace Smug\AdministrationBundle\Trait;

trait SecurityRightTrait
{
    public function getReadingRight(string $model): string
    {
        return 'read_' . $model;
    }

    public function getEditRight(string $model): string
    {
        return 'edit_' . $model;
    }
}