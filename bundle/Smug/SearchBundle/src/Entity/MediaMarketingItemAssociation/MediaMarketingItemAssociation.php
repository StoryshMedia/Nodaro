<?php

namespace Smug\SearchBundle\Entity\MediaMarketingItemAssociation;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\SearchBundle\Entity\MarketingItem\MarketingItem;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('media_search_window_marketing_item_association')]
class MediaMarketingItemAssociation extends BaseModel
{
    #[ManyToOne(targetEntity: Media::class)]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    protected Media $media;

    #[ManyToOne(targetEntity: MarketingItem::class, inversedBy: 'images')]
    #[JoinColumn(name: 'marketing_item_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected MarketingItem $item;
}
