<?php

namespace Smug\FrontendBundle\Entity\Seo;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Media\MediaSeoAssociation;

#[Entity]
#[Table('frontend_domain_seo')]
class Seo extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected $title;

    #[OneToOne(targetEntity: Domain::class, mappedBy: 'seo')]
    #[JoinColumn(name: 'domain_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected $domain;

    #[OneToMany(targetEntity: MediaSeoAssociation::class, mappedBy: 'seo')]
    #[BackendField(config: [
        'type' => 'ImageGallery',
        'placeholder' => 'IMAGES',
        'config' => [
            'controls' => [
                [
                    'label' => 'SET_MAIN_IMAGE',
                    'icon' => 'IconStar',
                    'type' => 'success',
                    'call' => '/be/api/smug/frontend/seo/images/main'
                ],
                [
                    'label' => 'DELETE',
                    'icon' => 'IconMinus',
                    'type' => 'danger',
                    'call' => '/be/api/smug/frontend/seo/images/delete',
                    'confirm' => true
                ]
            ],
            'getCall' => '/be/api/smug/frontend/seo/images/',
            'bypassId' => true
        ]
    ])]
    protected Collection $images;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }
}
