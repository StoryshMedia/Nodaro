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
    #[Groups(['public'])]
    protected $title;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $identifier;
    
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $configFile;

    #[Column(type: 'text')]
    #[Groups(['public'])]
    protected $description;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $template;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $externalSrc;

    #[Column(type: 'scriptField')]
    #[Groups(['public'])]
    protected $plainScript;

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    #[DefaultValue(false)]
    protected $installed;

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    #[DefaultValue(false)]
    protected bool $active;
    
    #[OneToMany(targetEntity: SiteScript::class, mappedBy: 'script')]
    protected Collection $siteScripts;

    #[OneToMany(targetEntity: ScriptField::class, mappedBy: 'script')]
    #[Groups(['public'])]
    protected Collection $fields;
}
