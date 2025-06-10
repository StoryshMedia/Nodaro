<?php

namespace Smug\FrontendBundle\Entity\SiteScript;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\Script\Script;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('frontend_site_script')]
class SiteScript extends BaseModel
{
    #[ManyToOne(targetEntity: Script::class, inversedBy: 'siteScripts')]
    #[JoinColumn(name: 'script_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['list'])]
    protected ?Script $script = null;

    #[ManyToOne(targetEntity: Site::class, inversedBy: 'siteScripts')]
    #[JoinColumn(name: 'site_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected Site $site;

    #[Column(type: 'integer')]
    #[DefaultValue(1)]
    #[BackendField(config: [
        'type' => 'Selectbox',
        'placeholder' => 'POSITION',
        'config' => [
            'items' => [
                [
                    'title' => 'HEAD_START',
                    'value' => 1
                ],
                [
                    'title' => 'HEAD_END',
                    'value' => 2
                ],
                [
                    'title' => 'FOOTER_START',
                    'value' => 3
                ],
                [
                    'title' => 'FOOTER_END',
                    'value' => 3
                ]
            ]
        ]
    ])]
    protected $area;

    #[Column(type: 'integer')]
    #[DefaultValue(0)]
    protected $position;
}
