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
    protected $identifier;

    #[Column(type: 'string')]
    protected $type;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $config;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $settings;

    #[Column(type: 'string')]
    protected $placeholder;

    #[Column(type: 'text')]
    protected $value;

    #[Column(type: 'boolean')]
    #[DefaultValue(false)]
    protected bool $isPlugin = false;

    #[Column(type: 'jsonField')]
    #[DefaultValue('[]')]
    protected $classes;

    #[Column(type: 'string')]
    protected $description;

    #[ManyToOne(targetEntity: Script::class, inversedBy: 'fields')]
    #[JoinColumn(name: 'script_id', referencedColumnName: 'id', onDelete: 'cascade', nullable: true)]
    #[Groups(['minimal'])]
    protected $script;
}
