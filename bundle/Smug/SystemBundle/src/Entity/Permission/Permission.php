<?php

namespace Smug\SystemBundle\Entity\Permission;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;

 #[Entity]
 #[Table('user_group_permission')]
class Permission extends BaseModel
{
    #[Column(type: 'string')]
    protected string $model;

    #[Column(type: 'string')]
    protected string $modelClass;

    #[Column(type: 'text')]
    protected string $disallowedFields = '';

    #[Column(type: 'text')]
    protected string $hiddenFields = '';

    #[Column(type: 'string')]
    protected string $type = '';

    #[Column(type: 'boolean')]
    protected bool $canRead = false;

    #[Column(type: 'boolean')]
    protected bool $canWrite = false;

	#[ManyToOne(targetEntity: UserGroup::class)]
	#[JoinColumn(name: 'userGroup_id', referencedColumnName: 'id')]
    protected UserGroup $userGroup;
}
