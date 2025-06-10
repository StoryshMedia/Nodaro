<?php

namespace Smug\FrontendBundle\Entity\ContentItemModuleTab;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;

 #[Entity]
 #[Table('frontend_site_content_item_module_tab')]
class ContentItemModuleTab extends BaseModel
{
    #[ManyToOne(targetEntity: ContentItemModule::class, inversedBy: 'tabs')]
    #[JoinColumn(name: 'field_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ContentItemModule $module;

    #[OneToMany(targetEntity: ContentItemModuleField::class, mappedBy: 'tab')]
    #[Groups(['list'])]
    protected Collection $fields;

    public function __construct() {
        $this->fields = new ArrayCollection();
    }
}
