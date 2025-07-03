<?php

namespace Smug\FrontendBundle\Entity\Module;

use Doctrine\Common\Collections\Collection;
use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\ModuleField\ModuleField;
use Smug\FrontendBundle\Entity\ModuleTab\ModuleTab;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('frontend_module')]
class Module extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    #[Groups(['public', 'subData'])]
    protected $title;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    #[Groups(['public', 'subData'])]
    protected $identifier;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'CATEGORY'
    ])]
    #[Groups(['public', 'subData'])]
    protected $category;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TYPE'
    ])]
    #[Groups(['public', 'subData'])]
    protected $type;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    #[Groups(['public', 'subData'])]
    protected $configFile;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[Groups(['public', 'subData'])]
    protected $multi;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[Groups(['public', 'subData'])]
    protected $installed;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[Groups(['public', 'subData'])]
    protected bool $active;

    #[Column(type: 'text')]
    #[Groups(['public', 'subData'])]
    protected $description;

    #[Column(type: 'text')]
    #[Groups(['public', 'subData'])]
    protected $template;

    #[OneToMany(targetEntity: ModuleField::class, mappedBy: 'module')]
    #[Groups(['public', 'subData'])]
    protected Collection $fields;

    #[Column(type: 'text')]
    #[DefaultValue('[]')]
    #[Groups(['public', 'subData'])]
    protected string $scripts = '[]';

    #[OneToMany(targetEntity: ModuleTab::class, mappedBy: 'module')]
    #[Groups(['public', 'subData'])]
    protected Collection $tabs;
}
