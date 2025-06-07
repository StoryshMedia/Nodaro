<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field;

use Smug\AdministrationBundle\Service\Components\Factories\View\Field;

class DatepickerField extends Field
{
    public function setDisabled(bool $disabled = false): void
    {
        $this->addConfigItem('disabled', $disabled);
    }

    public function getDisabled(): array|bool|string|int|null
    {
        return $this->getConfigItem('disabled');
    }
    
    public function setValueType(bool $valueType = false): void
    {
        $this->addConfigItem('valueType', $valueType);
    }

    public function getValueType(): array|bool|string|int|null
    {
        return $this->getConfigItem('valueType');
    }
}
