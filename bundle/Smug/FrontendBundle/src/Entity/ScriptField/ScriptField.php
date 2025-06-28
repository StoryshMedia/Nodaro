<?php

namespace Smug\FrontendBundle\Entity\ScriptField;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Symfony\Component\Serializer\Attribute\Groups;
use Smug\FrontendBundle\Entity\Script\Script;

#[Entity]
#[Table('frontend_script_field')]
class ScriptField extends BaseModel
{
    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $identifier;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $type;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $config;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $settings;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $placeholder;

    #[Column(type: 'text')]
    #[Groups(['public'])]
    protected $value;

    #[Column(type: 'boolean')]
    #[Groups(['public'])]
    #[DefaultValue(false)]
    protected bool $isPlugin = false;

    #[Column(type: 'jsonField')]
    #[Groups(['public'])]
    #[DefaultValue('[]')]
    protected $classes;

    #[Column(type: 'string')]
    #[Groups(['public'])]
    protected $description;

    #[ManyToOne(targetEntity: Script::class, inversedBy: 'fields')]
    #[JoinColumn(name: 'script_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['public'])]
    protected $script;
}
