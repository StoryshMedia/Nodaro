<?php

namespace Smug\FrontendBundle\Entity\Domain;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Smug\FrontendBundle\Entity\Seo\Seo;

#[Entity]
#[Table('frontend_domain')]
class Domain extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected $title;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'URL'
    ])]
    protected $url;

    #[OneToMany(targetEntity: Site::class, mappedBy: 'domain')]
    #[Groups(['list', 'nested'])]
    #[BackendField(config: [
        'type' => 'SiteTree',
        'placeholder' => 'SITE_TREE',
        'config' => [
            'sites' => [
                'save' => '/be/api/smug/frontend/site/save'
            ]
        ]
    ])]
    protected $sites;

    #[OneToOne(targetEntity: Seo::class, mappedBy: 'domain')]
    #[JoinColumn(name: 'seo_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected $seo;

    #[Column(type: 'string')]
    #[DefaultValue('@SmugFrontend/frontend/index/index.html.twig')]
    #[BackendField(config: [
        'type' => 'Selectbox',
        'placeholder' => 'TEMPLATE',
        'config' => [
            'getCall' => '/be/api/custom/template/list'
        ]
    ])]
    protected $templateString;

    public function __construct()
    {
        $this->sites = new ArrayCollection();
    }
}
