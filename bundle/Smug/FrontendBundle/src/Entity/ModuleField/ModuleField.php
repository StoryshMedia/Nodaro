<?php

namespace Smug\FrontendBundle\Entity\ModuleField;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\Module\Module;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\OneToMany;
use Smug\FrontendBundle\Entity\ModuleTab\ModuleTab;

#[Entity]
#[Table('frontend_module_field')]
class ModuleField extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $identifier;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $type;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $config;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $settings;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $placeholder;

    #[Column(type: 'text')]
    #[Groups(['public'])]
    protected $defaultValue;

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    #[DefaultValue(false)]
    protected bool $isPlugin = false;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $classes;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $description;

    #[ManyToOne(targetEntity: Module::class, inversedBy: 'fields')]
    #[JoinColumn(name: 'module_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['public'])]
    protected $module;

    #[OneToMany(targetEntity: ModuleField::class, mappedBy: 'parentId')]
    #[Groups(['public'])]
    protected Collection $children;

    #[ManyToOne(targetEntity: ModuleField::class, inversedBy: 'children')]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['public'])]
    protected ?ModuleField $parentId;

    #[ManyToOne(targetEntity: ModuleTab::class, inversedBy: 'fields')]
    #[JoinColumn(name: 'tab_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['public'])]
    protected ?ModuleTab $tab = null;

    public function __construct() {
        $this->children = new ArrayCollection();
    }
}
