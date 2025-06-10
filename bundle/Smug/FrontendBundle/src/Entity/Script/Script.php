<?php

namespace Smug\FrontendBundle\Entity\Script;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\FrontendBundle\Entity\SiteScript\SiteScript;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\Common\Collections\Collection;
use Smug\FrontendBundle\Entity\ScriptField\ScriptField;

#[Entity]
#[Table('frontend_script')]
class Script extends BaseModel
{
    #[Column(type: 'string')]
    protected $title;

    #[Column(type: 'string')]
    protected $identifier;
    
    #[Column(type: 'string')]
    protected $configFile;

    #[Column(type: 'text')]
    protected $description;

    #[Column(type: 'string')]
    protected $template;

    #[Column(type: 'string')]
    protected $externalSrc;

    #[Column(type: 'scriptField')]
    protected $plainScript;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected $installed;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected bool $active;
    
    #[OneToMany(targetEntity: SiteScript::class, mappedBy: 'script')]
    #[Groups(['minimal'])]
    protected Collection $siteScripts;

    #[OneToMany(targetEntity: ScriptField::class, mappedBy: 'script')]
    #[Groups(['list'])]
    protected Collection $fields;
}
