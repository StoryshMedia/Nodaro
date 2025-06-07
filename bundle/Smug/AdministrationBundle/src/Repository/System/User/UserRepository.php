<?php

namespace Smug\AdministrationBundle\Repository\System\User;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

abstract class UserRepository extends EntityRepository implements UserLoaderInterface
{
	public function loadUserByUsername(string $username)
	{
		return $this->findOneBy(['userName' => $username]);
	}
}
