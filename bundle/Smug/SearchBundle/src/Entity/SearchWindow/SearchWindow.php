<?php

namespace Smug\SearchBundle\Entity\SearchWindow;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\SearchBundle\Entity\ListItem\ListItem;
use Smug\SearchBundle\Entity\MarketingItem\MarketingItem;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('search_window')]
class SearchWindow extends BaseModel
{
    #[Column(type: 'string')]
    #[DefaultValue('/')]
    protected string $searchDetailLink;

    #[Column(type: 'htmlField')]
    #[DefaultValue('{}')]
    protected string $detailPages;

    #[OneToMany(targetEntity: MarketingItem::class, mappedBy: 'searchWindow')]
    #[Groups(['list'])]
    protected Collection $marketingItems;

    #[OneToMany(targetEntity: ListItem::class, mappedBy: 'searchWindow')]
    #[Groups(['list'])]
    protected Collection $listItems;

    #[OneToOne(targetEntity: Domain::class, mappedBy: 'searchWindow')]
    #[JoinColumn(name: 'domain_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    #[Groups(['minimal'])]
    protected $domain;

    public function __construct()
    {
        $this->marketingItems = new ArrayCollection();
        $this->listItems = new ArrayCollection();
    }
}
