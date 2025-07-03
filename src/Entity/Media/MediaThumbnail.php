<?php

namespace Smug\Core\Entity\Media;

use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\BaseModel;

#[Entity]
#[Table('media_thumbnail')]
class MediaThumbnail extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $file;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $path;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $extension;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $size;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $sizeX;

    #[Column(type: 'integer')]
    #[Groups(['public'])]
    protected int $sizeY;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $variant;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $viewport;

    #[ManyToOne(targetEntity: Media::class, inversedBy: 'thumbnails')]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['public'])]
    protected Media $media;
}
