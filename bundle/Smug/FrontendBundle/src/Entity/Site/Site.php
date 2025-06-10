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
    #[Column(type: 'string')]
    protected $title;

    #[Column(type: 'string')]
    #[NotBlank]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SLUG'
    ])]
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
    protected $hiddenInMenu;

    #[ManyToOne(targetEntity: Domain::class, inversedBy: 'sites')]
    #[JoinColumn(name: 'domain_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected Domain $domain;

    #[Column(type: 'jsonField')]
    #[Groups(['show'])]
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
    protected $seoTitle;

    #[Column(type: 'text')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Textarea',
        'placeholder' => 'SEO_DESCRIPTION'
    ])]
    protected $seoDescription;

    #[Column(type: 'string')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SEO_KEYWORDS'
    ])]
    protected $seoKeywords;

    #[Column(type: 'string')]
    #[NotBlank]
    #[DefaultValue('')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'CANONICAL_LINK'
    ])]
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
    protected $noFollow;

    #[OneToMany(targetEntity: ContentItem::class, mappedBy: 'site')]
    #[Groups(['list'])]
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
    #[Groups(['list'])]
    protected Collection $siteScripts;

    #[Column(type: 'jsonField')]
    #[DefaultValue([])]
    #[Groups(['list'])]
    protected string|array $siteStyles = '[]';

    #[Column(type: 'string')]
    protected string $parentId = '';

    public function __construct()
    {
        $this->contentItems = new ArrayCollection();
        $this->siteScripts = new ArrayCollection();
    }
}
