<?php

namespace Smug\SystemBundle\Entity\UserGroup;

use Smug\Core\Entity\Base\BaseModel;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\SystemBundle\Entity\Permission\Permission;
use Smug\SystemBundle\Entity\User\User;
use Symfony\Component\Serializer\Attribute\Groups;

 #[Entity]
 #[Table('user_group')]
class UserGroup extends BaseModel
{
    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'TITLE'
    ])]
    protected string $title;

    #[Column(type: 'text')]
    #[BackendField(config: [
        'type' => 'Editor',
        'placeholder' => 'DESCRIPTION',
        'config' => [
            'mentions' => false
        ]
    ])]
    protected string $description;

    #[Column(type: 'boolean')]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'IS_ADMIN'
    ])]
    protected bool $admin = false;

    #[OneToMany(targetEntity: User::class, mappedBy: 'userGroup')]
    #[Groups(['minimal'])]
    #[BackendField(config: [
        'type' => 'Table',
        'placeholder' => '',
        'config' => [
            'columns' => [
                [
                    'identifier' => 'username',
                    'type' => 'string'
                ]
            ],
            'controls' => [
                [
                    'type' => 'function',
                    'config' => [
                        'confirm' => true,
                        'text' => 'DELETE_CONFIRMATION_TEXT',
                        'headline' => 'DELETE_CONFIRMATION_HEADLINE',
                        'icon' => 'IconTrash',
                        'call' => '/be/api/smug/publication/publication_review/delete'
                    ]
                ]
            ]
        ]
    ])]
    protected Collection $member;

    #[OneToMany(targetEntity: Permission::class, mappedBy: 'userGroup')]
    #[Groups(['minimal'])]
    #[BackendField(config: [
        'type' => 'UserPermission',
        'placeholder' => ''
    ])]
    protected Collection $permission;
}
