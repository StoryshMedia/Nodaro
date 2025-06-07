<?php

namespace Smug\AdministrationBundle\Security;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // set the new encoded password on the User object
        $user->__set('password', $newHashedPassword);

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username)
    {
        $user = $this->findUser($username);

        if (!$user) {
            throw new Exception(sprintf('Username "%s" does not exist.', $username));
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Expected an instance of %s, but got "%s".', \Smug\SystemBundle\Entity\User\User::class, get_class($user)));
        }

        if (null === $reloadedUser = $this->em->getRepository(\Smug\SystemBundle\Entity\User\User::class)->findOneBy(['id' => $user->__get('id')])) {
            throw new Exception(sprintf('User with ID "%s" could not be reloaded.', $user->__get('id')));
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class)
    {
        $userClass = EntityGenerator::getGeneratedEntity(\Smug\SystemBundle\Entity\User\User::class);

        return $userClass === $class || is_subclass_of($class, $userClass);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        /** @var \Smug\SystemBundle\Entity\User\User $user */
        $user = $this->em->getRepository(
            EntityGenerator::getGeneratedEntity(\Smug\SystemBundle\Entity\User\User::class)
            )->findOneBy(['username' => $identifier]);

        return $user;
    }

    /**
     * @param $username
     * @return object|null
     */
    protected function findUser($username)
    {
        return $this->em->getRepository(
            EntityGenerator::getGeneratedEntity(\Smug\SystemBundle\Entity\User\User::class)
            )->findOneBy(['username' => $username]);
    }
}
