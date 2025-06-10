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
    protected $title;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected $identifier;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'CATEGORY'
    ])]
    protected $category;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TYPE'
    ])]
    protected $type;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected $configFile;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected $multi;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected $installed;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected bool $active;

    #[Column(type: 'text')]
    protected $description;

    #[Column(type: 'text')]
    protected $template;

    #[OneToMany(targetEntity: ModuleField::class, mappedBy: 'module')]
    #[Groups(['list'])]
    protected Collection $fields;

    #[Column(type: 'text')]
    #[DefaultValue('[]')]
    protected string $scripts = '[]';

    #[OneToMany(targetEntity: ModuleTab::class, mappedBy: 'module')]
    #[Groups(['list'])]
    protected Collection $tabs;
}
