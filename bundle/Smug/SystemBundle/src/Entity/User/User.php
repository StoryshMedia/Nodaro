<?php

namespace Smug\SystemBundle\Entity\User;

use Smug\Core\Entity\Base\UserBaseModel;
use Smug\SystemBundle\Entity\Language\Language;
use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Smug\Core\Entity\Base\Attribute\BackendField;
use Smug\Core\Entity\Base\Attribute\DefaultValue;
use Smug\SystemBundle\Entity\Media\MediaUserAssociation;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\Serializer\Attribute\Groups;

#[Entity]
#[Table('user')]
class User extends UserBaseModel
{
	#[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'USERNAME'
    ])]
	protected string $username;
	
	#[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'USERNAME_CANONICAL'
    ])]
	protected string $usernameCanonical;
	
	#[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Email',
        'placeholder' => 'EMAIL'
    ])]
	protected string $email;
	
	#[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Email',
        'placeholder' => 'EMAIL_CANONICAL'
    ])]
	protected string $emailCanonical;

    #[Column(type: 'string', nullable: true)]
	protected ?string $salt = null;

    #[Column(type: 'boolean')]
    #[BackendField(config: [
        'type' => 'Checkbox',
        'placeholder' => 'ENABLED'
    ])]
    protected bool $enabled = false;

    #[Column(type: 'string')]
    #[BackendField(config: [
        'type' => 'Password',
        'placeholder' => 'PASSWORD'
    ])]
	protected string $password;
	
	#[Column(type: 'datetime', nullable: true)]
    #[BackendField(config: [
        'type' => 'Datepicker',
        'placeholder' => 'LAST_LOGIN',
        'config' => [
            'valueType' => 'date',
            'disabled' => true
        ],
    ])]
	protected ?DateTime $lastLogin;
	
    #[Column(type: 'string', nullable: true)]
	protected ?string $confirmationToken;
	
	#[Column(type: 'datetime', nullable: true)]
    #[BackendField(config: [
        'type' => 'Datepicker',
        'placeholder' => 'PASSWORD_REQUESTED_AT',
        'config' => [
            'valueType' => 'date',
            'disabled' => true
        ],
    ])]
	protected ?DateTime $passwordRequestedAt;
	
    #[Column(type: 'text')]
    #[DefaultValue('a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}')]
	protected string $roles = 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}';
	
	#[ManyToOne(targetEntity: UserGroup::class, inversedBy: 'member')]
	#[JoinColumn(name: 'userGroup_id', referencedColumnName: 'id', nullable: true)]
    #[BackendField(config: [
        'type' => 'Selectbox',
        'placeholder' => 'USER_GROUP',
        'autocomplete' => false,
        'config' => [
            'getCall' => '/be/api/smug/system/user_group'
        ]
    ])]
	protected ?UserGroup $userGroup;
	
	#[ManyToOne(targetEntity: Language::class)]
	#[JoinColumn(name: 'language_id', referencedColumnName: 'id')]
    #[BackendField(config: [
        'type' => 'Selectbox',
        'placeholder' => 'LANGUAGE',
        'autocomplete' => false,
        'config' => [
            'getCall' => '/be/api/smug/system/language'
        ]
    ])]
	protected Language $language;
	
    #[Column(type: 'string', nullable: true)]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'NAME'
    ])]
	protected string $name;
	
    #[Column(type: 'string', nullable: true)]
    #[BackendField(config: [
        'type' => 'Text',
        'placeholder' => 'SURNAME'
    ])]
	protected ?string $surname;

    #[OneToMany(targetEntity: MediaUserAssociation::class, mappedBy: 'user')]
    #[Groups(['list'])]
    #[BackendField(config: [
        'type' => 'ImageGallery',
        'placeholder' => 'IMAGES',
        'config' => [
            'controls' => [
                [
                    'label' => 'SET_MAIN_IMAGE',
                    'icon' => 'IconStar',
                    'type' => 'success',
                    'call' => '/be/api/custom/user/profile/image/main'
                ],
                [
                    'label' => 'DELETE',
                    'icon' => 'IconMinus',
                    'type' => 'danger',
                    'call' => '/be/api/custom/user/profile/image/delete',
                    'confirm' => true
                ]
            ],
            'bypassId' => true
        ]
    ])]
    protected Collection $images;

    public function __construct()
    {
	    $this->enabled = false;
        $this->images = new ArrayCollection();
    }

    public function getFeedData(): array
    {
        return [
            'id' => '@' . $this->__get('username'),
            'userId' => $this->getId(),
            'name' => $this->__get('name')
        ];
    }
}
