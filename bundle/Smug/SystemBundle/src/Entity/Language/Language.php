<?php

namespace Smug\SystemBundle\Entity\Language;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('language')]
class Language extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    #[Groups(['public'])]
    protected string $title = '';
    
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'LOCALE'
    ])]
    #[Groups(['public'])]
    protected string $locale = '';

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'AREA'
    ])]
    #[Groups(['public'])]
    protected string $area = '';
    
    #[Column(type: 'boolean')]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'TRANSLATION_AVAILABLE'
    ])]
    #[Groups(['public'])]
    protected bool $translationAvailable = false;
}
