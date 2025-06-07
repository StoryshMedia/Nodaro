<?php

namespace Smug\AdministrationBundle\Preparer\User;

use Smug\SystemBundle\Entity\Language\Language;
use Smug\SystemBundle\Entity\User\User;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Smug\Core\Exception\Base\NotValidException;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayFieldProvider;
use Smug\Core\Service\Base\Query\QueryMapper;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use \DateTime;

/**
 * Class UserPreparer
 * @package Smug\Core\Preparer\User\User
 */
class UserPreparer extends QueryMapper
{
	/**
	 * @param array $user
	 * @param UserPasswordEncoder $encoder
	 * @param User|null $newUser
	 * @param Language|null $language
	 * @param UserGroup|null $group
	 * @return array
	 * @throws NotValidException
	 */
    public function prepare(
    	array $user,
	    UserPasswordEncoder $encoder,
	    User $newUser = null,
	    Language $language = null,
	    UserGroup $group = null
    ): array {
        if (DataHandler::doesKeyExists('password', $user) && DataHandler::isEmpty($user['password'])) {
	        throw new NotValidException('Validation failed because of empty Password');
        }

        // only set password on adding a user.
	    // password is changed in a separate function
        if (!DataHandler::isEmpty($newUser)) {
	        $user['password'] = $encoder->encodePassword($newUser, $user['password']);
        }

        return ArrayFieldProvider::addAdditionalFields(
        	$user,
	        [
	        	'language' => $language,
		        'userGroup' => $group
	        ]
        );
    }

    /**
     * @param User $user
     * @return User
     */
    public function preparePasswordChange(User $user): User
    {
        $user->setPasswordRequestedAt(new DateTime());

        return $user;
    }
}
