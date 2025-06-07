<?php

namespace Smug\AdministrationBundle\Service\User\Add;

use Smug\SystemBundle\Entity\Language\Language;
use Smug\Core\Entity\System\Setting\Settings;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SystemBundle\Entity\User\User;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Smug\AdministrationBundle\Preparer\User\UserPreparer;
use Smug\AdministrationBundle\Validator\User\UserValidator;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\FileContentProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Interfaces\AddServiceInterface;
use Smug\Core\Service\Base\Service\BaseService;
use \Exception;
use Smug\Core\Context\Context;

/**
 * Class AddService
 * @package Smug\Core\Service\User\User\User\Add
 */
class AddService extends BaseService implements AddServiceInterface
{
    /**
     * @inheritDoc
     */
    public function add(Context $context, $import = false): array
    {
        $userRepository = $this->em->getRepository(User::class);

        $userFromDb = $userRepository->findOneBy(['email' => $data['email']]);

        if ($userFromDb) {
            return [
                'success' => false,
                'code' => 500,
                'message' => 'Diese E-Mail Adresse ist bereits registriert',
            ];
        }
        try {
            /** @var UserPreparer $preparer */
	        $preparer = ServiceGenerationFactory::createInstance(UserPreparer::class);
            /** @var UserValidator $validator */
	        $validator = ServiceGenerationFactory::createInstance(UserValidator::class);
            /** @var \Smug\Core\Service\Dashboard\Dashboard\Add\AddService $dashboardService */
            $dashboardService = ServiceGenerationFactory::createInstance(\Smug\Core\Service\Dashboard\Dashboard\Add\AddService::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        $companies = $data['companies'];

        /** @var UserGroup $userGroup */
        $userGroup = $this->mapValue($data, UserGroup::class, false, 'userGroup');
        /** @var Language $language */
        $language = $this->mapValue($data, Language::class, false, 'language');

	    $newUser = new User();
	
	    try {
		    $user = $preparer->prepare($data,
			    $this->getContainerVariable('security.password_encoder'),
			    $newUser,
			    $language,
			    $userGroup
		    );
		    
		    $user = $validator->validate($user);
	    } catch (Exception $exception) {
		    return ExceptionProvider::getException($exception);
	    }
	    
	    $newUser->setEmail($user['email']);
	    $newUser->setUsername($user['name']);
	    $newUser->setPassword($user['password']);
	    $newUser->setRoles($user['roles']);
	    //$newUser->setImage($user['image']);
	    $newUser->setLanguage($user['language']);
	    $newUser->setUserGroup($user['userGroup']);
	    $newUser->setName($user['firstName']);
	    $newUser->setSurname($user['lastName']);
	    $newUser->setUsernameCanonical($user['nameCanonical']);
	    $newUser->setEmailCanonical($user['emailCanonical']);
	    
        $this->em->persist($newUser);
        $this->em->flush();

        $defaultShortCutSettings = FileContentProvider::getSystemFileContent('defaultShortCutSettings.json');

        $return = new Settings();

        $return->setUser($newUser);
        $return->setSettings(DataHandler::getJsonEncode($defaultShortCutSettings));

        $this->em->persist($return);
        $this->em->flush();

        $dashboardService->add(['user' => $newUser]);


        return [
            'success' => true,
            'data' => $newUser->toArray()
        ];
    }
}
