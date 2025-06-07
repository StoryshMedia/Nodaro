<?php

namespace Smug\SystemBundle\Entity\Language;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;

#[Entity]
#[Table('language')]
class Language extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected string $title = '';
    
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'LOCALE'
    ])]
    protected string $locale = '';

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'AREA'
    ])]
    protected string $area = '';
    
    #[Column(type: 'boolean')]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'TRANSLATION_AVAILABLE'
    ])]
    protected bool $translationAvailable = false;
}
