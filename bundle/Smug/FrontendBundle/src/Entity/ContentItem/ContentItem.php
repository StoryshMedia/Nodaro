<?php

namespace Smug\FrontendBundle\Entity\ContentItem;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\Core\Entity\Base\Attribute\EntityAccess;
use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\Site\Site;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[EntityAccess(['fe', 'be', 'cli'])]
 #[Table('frontend_site_content_item')]
class ContentItem extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected $title;

    #[Column(type: 'integer')]
    #[BackendField(config: [
        'type' => 'Number',
        'placeholder' => 'POSITION'
    ])]
    protected int $position;

    #[Column(type: 'integer')]
    #[BackendField(config: [
        'type' => 'Number',
        'placeholder' => 'COLUMN'
    ])]
    protected int $rowColumn;
    
    #[ManyToOne(targetEntity: ContentItemModule::class, inversedBy: 'content')]
    #[JoinColumn(name: 'module_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['list'])]
    protected ContentItemModule $module;

    #[ManyToOne(targetEntity: Site::class, inversedBy: 'contentItems')]
    #[JoinColumn(name: 'site_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected ?Site $site;

    #[Column(type: 'text')]
    #[DefaultValue('[]')]
    protected string $additionalClasses = '[]';

    #[Column(type: 'text')]
    #[DefaultValue('[]')]
    protected string $templateClasses = '[]';

    #[Column(type: 'string')]
    protected string $parentId = '';
}
