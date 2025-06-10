<?php

namespace Smug\FrontendBundle\Entity\ContentItemModule;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Smug\FrontendBundle\Entity\ContentItemModuleTab\ContentItemModuleTab;
use Smug\FrontendBundle\Entity\Module\Module;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[MappedSuperclass]
 #[Table('frontend_site_content_item_module')]
class ContentItemModule extends BaseModel
{
    #[ManyToOne(targetEntity: Module::class, inversedBy: 'contentItemModules')]
    #[JoinColumn(name: 'module_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected $module;

    #[OneToOne(targetEntity: ContentItem::class, mappedBy: 'module')]
    #[JoinColumn(name: 'content_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?ContentItem $content = null;

    #[OneToMany(targetEntity: ContentItemModuleField::class, mappedBy: 'module')]
    #[Groups(['list'])]
    protected Collection $fields;

    #[OneToMany(targetEntity: ContentItemModuleTab::class, mappedBy: 'module')]
    #[Groups(['list'])]
    protected Collection $tabs;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->tabs = new ArrayCollection();
    }
}
