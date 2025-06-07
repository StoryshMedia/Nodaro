<?php

namespace Smug\SystemBundle\Entity\Media;

use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Entity\Media\Media;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('media_user_association')]
class MediaUserAssociation extends BaseModel
{
    #[ManyToOne(targetEntity: Media::class)]
    #[JoinColumn(name: 'media_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    protected Media $media;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'images')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected User $user;

    #[Column(type: 'boolean')]
    protected bool $main;

    #[Column(type: 'boolean')]
    protected bool $approved = true;
}
