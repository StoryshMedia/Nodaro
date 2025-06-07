<?php declare(strict_types=1);

namespace Smug\Core\Entity\Base;

use Doctrine\ORM\Mapping\MappedSuperclass;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[MappedSuperclass]
#[HasLifecycleCallbacks]
class UserBaseModel extends BaseModel implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @inheritDoc
     */
	public function getUserIdentifier(): string
    {
        return $this->__get('email');
    }

	/**
	 * {@inheritdoc}
	 */
	public function eraseCredentials()
	{
	}

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return mixed|string[]
     */
    public function getRoles(): array
    {
        return unserialize($this->roles);
    }
	
	/**
	 * @param array $roles
	 */
    public function setRoles(array $roles)
    {
        $this->roles = serialize($roles);
    }
	
	/**
	 * @return string
	 */
	public function getPassword (): string
	{
		return $this->password;
	}

    public function getUserGroup()
    {
        return $this->userGroup;
    }
}
