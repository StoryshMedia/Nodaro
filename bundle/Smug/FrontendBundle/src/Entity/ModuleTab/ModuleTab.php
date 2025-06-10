<?php

namespace Smug\FrontendBundle\Entity\ModuleTab;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;
use Smug\FrontendBundle\Entity\Module\Module;
use Smug\FrontendBundle\Entity\ModuleField\ModuleField;

 #[Entity]
 #[Table('frontend_module_tab')]
class ModuleTab extends BaseModel
{
    #[ManyToOne(targetEntity: Module::class, inversedBy: 'tabs')]
    #[JoinColumn(name: 'module_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected Module $module;

    #[OneToMany(targetEntity: ModuleField::class, mappedBy: 'tab')]
    #[Groups(['list'])]
    protected Collection $fields;

    public function __construct() {
        $this->fields = new ArrayCollection();
    }
}
