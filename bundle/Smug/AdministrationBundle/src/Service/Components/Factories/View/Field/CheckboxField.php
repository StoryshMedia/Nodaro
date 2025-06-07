<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field;

use Smug\AdministrationBundle\Service\Components\Factories\View\Field;

class CheckboxField extends Field
{
    public function setDisabled(bool $disabled = false): void
    {
        $this->addConfigItem('disabled', $disabled);
    }

    public function getDisabled(): array|bool|string|int|null
    {
        return $this->getConfigItem('disabled');
    }
}
