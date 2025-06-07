<?php

namespace Smug\AdministrationBundle\Service\Components\Factories\View\Field;

use Smug\AdministrationBundle\Service\Components\Factories\View\Field;

class ButtonField extends Field
{
    public function setButtonText(bool $disabled = false): void
    {
        $this->addConfigItem('buttonText', $disabled);
    }

    public function getButtonText(): array|bool|string|int|null
    {
        return $this->getConfigItem('buttonText');
    }
    
    public function setButtonType(bool $buttonType = false): void
    {
        $this->addConfigItem('buttonType', $buttonType);
    }

    public function getButtonType(): array|bool|string|int|null
    {
        return $this->getConfigItem('buttonType');
    }
    
    public function setFunctionCall(bool $functionCall = false): void
    {
        $this->addConfigItem('functionCall', $functionCall);
    }

    public function getFunctionCall(): array|bool|string|int|null
    {
        return $this->getConfigItem('functionCall');
    }
    
    public function setSuccessHandling(array $successHandling = ['type' => 'reload']): void
    {
        $this->addConfigItem('successHandling', $successHandling);
    }

    public function getSuccessHandling(): array|bool|string|int|null
    {
        return $this->getConfigItem('successHandling');
    }
}
