<?php

namespace Smug\SearchBundle\Entity\MarketingItem;

use Doctrine\Common\Collections\Collection;
use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\SearchBundle\Entity\MediaMarketingItemAssociation\MediaMarketingItemAssociation;
use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('search_window_marketing_item')]
class MarketingItem extends BaseModel
{
    #[Column(type: 'string')]
    protected string $linkTarget;

    #[Column(type: 'string')]
    protected string $headline;

    #[Column(type: 'string')]
    protected string $captionText;

    #[ManyToOne(targetEntity: SearchWindow::class, inversedBy: 'marketingItems')]
    #[JoinColumn(name: 'search_window_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?SearchWindow $searchWindow;

    #[OneToMany(targetEntity: MediaMarketingItemAssociation::class, mappedBy: 'item')]
    #[Groups(['list'])]
    protected Collection $images;

    #[Column(type: 'boolean')]
    protected bool $hidden = false;
}
