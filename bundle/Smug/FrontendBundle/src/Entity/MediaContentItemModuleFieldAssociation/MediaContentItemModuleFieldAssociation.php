<?php

namespace Smug\FrontendBundle\Entity\MediaContentItemModuleFieldAssociation;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\FrontendBundle\Entity\ContentItemModuleField\ContentItemModuleField;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('media_content_item_module_field_association')]
class MediaContentItemModuleFieldAssociation extends BaseModel
{
    #[ManyToOne(targetEntity: Media::class)]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    protected Media $media;

    #[ManyToOne(targetEntity: ContentItemModuleField::class, inversedBy: 'files')]
    #[JoinColumn(name: 'field_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ContentItemModuleField $field;
}
