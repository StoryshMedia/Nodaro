<?php

namespace Smug\FrontendBundle\Entity\Media;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\FrontendBundle\Entity\Seo\Seo;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('media_domain_seo_association')]
class MediaSeoAssociation extends BaseModel
{
    #[ManyToOne(targetEntity: Media::class)]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    protected Media $media;

    #[ManyToOne(targetEntity: Seo::class, inversedBy: 'files')]
    #[JoinColumn(name: 'seo_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected Seo $seo;
}