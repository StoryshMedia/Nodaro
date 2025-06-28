<?php

namespace Smug\FrontendBundle\Entity\Site;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\ContentItem\ContentItem;
use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity]
#[Table('frontend_site')]
class Site extends BaseModel
{
    protected array $children = [];

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $title;

    #[Column(type: 'string')]
    #[NotBlank]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SLUG'
    ])]
    #[Groups(['public'])]
    protected $slug;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'ROOT_PAGE',
        'config' => [
            'trueLabel' => 'YES',
            'falseLabel' => 'NO'
        ]
    ])]
    #[Groups(['private'])]
    protected $rootPage;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'HIDDEN',
        'config' => [
            'trueLabel' => 'YES',
            'falseLabel' => 'NO'
        ]
    ])]
    #[Groups(['public'])]
    protected $hidden;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'HIDDEN_IN_MENU',
        'config' => [
            'trueLabel' => 'YES',
            'falseLabel' => 'NO'
        ]
    ])]
    #[Groups(['public'])]
    protected $hiddenInMenu;

    #[ManyToOne(targetEntity: Domain::class, inversedBy: 'sites')]
    #[JoinColumn(name: 'domain_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal', 'public'])]
    protected Domain $domain;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue([])]
    #[BackendField(config: [
        'type' => 'Seo',
        'placeholder' => 'SEO_SETTINGS'
    ])]
    protected string|array $seoData;

    #[Column(type: 'string')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SEO_TITLE'
    ])]
    #[Groups(['public'])]
    protected $seoTitle;

    #[Column(type: 'text')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Textarea',
        'placeholder' => 'SEO_DESCRIPTION'
    ])]
    #[Groups(['public'])]
    protected $seoDescription;

    #[Column(type: 'string')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SEO_KEYWORDS'
    ])]
    #[Groups(['public'])]
    protected $seoKeywords;

    #[Column(type: 'string')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'CANONICAL_LINK'
    ])]
    #[Groups(['public'])]
    protected $canonicalLink;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'NO_INDEX',
        'config' => [
            'trueLabel' => 'YES',
            'falseLabel' => 'NO'
        ]
    ])]
    #[Groups(['public'])]
    protected $noIndex;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'NO_FOLLOW',
        'config' => [
            'trueLabel' => 'YES',
            'falseLabel' => 'NO'
        ]
    ])]
    #[Groups(['public'])]
    protected $noFollow;

    #[OneToMany(targetEntity: ContentItem::class, mappedBy: 'site')]
    #[Groups(['list', 'public'])]
    #[BackendField(config: [
        'type' => 'Content',
        'placeholder' => 'CONTENT',
        'config' => [
            'modules' => [
                'getCall' => '/be/api/smug/frontend/module',
                'refreshCall' => '/be/api/custom/module/rerender',
                'addItemCall' => '/be/api/smug/frontend/contentItem'
            ]
        ]
    ])]
    protected Collection $contentItems;

    #[OneToMany(targetEntity: SiteScript::class, mappedBy: 'site')]
    #[Groups(['list', 'public'])]
    protected Collection $siteScripts;

    #[Column(type: 'jsonField')]
    #[DefaultValue([])]
    #[Groups(['list', 'public'])]
    protected string|array $siteStyles = '[]';

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected string $parentId = '';

    public function __construct()
    {
        $this->contentItems = new ArrayCollection();
        $this->siteScripts = new ArrayCollection();
    }
}
