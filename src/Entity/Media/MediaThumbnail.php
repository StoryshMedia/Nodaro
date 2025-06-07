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
    protected string $file;

    #[Column(type: 'string')]
    protected string $path;

    #[Column(type: 'string')]
    protected string $extension;

    #[Column(type: 'integer')]
    protected int $size;

    #[Column(type: 'integer')]
    protected int $sizeX;

    #[Column(type: 'integer')]
    protected int $sizeY;

    #[Column(type: 'string')]
    protected string $variant;

    #[Column(type: 'string')]
    protected string $viewport;

    #[ManyToOne(targetEntity: Media::class, inversedBy: 'thumbnails')]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected Media $media;
}
