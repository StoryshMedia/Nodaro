<?php

namespace Smug\FrontendBundle\Entity\ContentItemModuleField;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\ContentItemModuleTab\ContentItemModuleTab;
use Smug\FrontendBundle\Entity\MediaContentItemModuleFieldAssociation\MediaContentItemModuleFieldAssociation;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('frontend_site_content_item_module_field')]
class ContentItemModuleField extends BaseModel
{
    #[ManyToOne(targetEntity: ContentItemModule::class, inversedBy: 'contentFields')]
    #[JoinColumn(name: 'field_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?ContentItemModule $module = null;

    #[Column(type: 'string')]
    protected $type;

    #[Column(type: 'string')]
    protected $identifier;

    #[Column(type: 'string')]
    protected $placeholder;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $config;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $settings;

    #[Column(type: 'htmlField')]
    #[DefaultValue('')]
    protected string $value = '';

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected bool $isPlugin = false;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $classes;

    #[Column(type: 'string')]
    protected $description;

    #[OneToMany(targetEntity: MediaContentItemModuleFieldAssociation::class, mappedBy: 'field')]
    #[Groups(['list'])]
    protected Collection $files;

    #[OneToMany(targetEntity: ContentItemModuleField::class, mappedBy: 'parentId')]
    protected Collection $children;

    #[ManyToOne(targetEntity: ContentItemModuleField::class, inversedBy: 'children')]
    #[JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?ContentItemModuleField $parentId = null;

    #[ManyToOne(targetEntity: ContentItemModuleTab::class, inversedBy: 'fields')]
    #[JoinColumn(name: 'tab_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?ContentItemModuleTab $tab = null;

    public function __construct() {
        $this->children = new ArrayCollection();
        $this->files = new ArrayCollection();
    }
}
