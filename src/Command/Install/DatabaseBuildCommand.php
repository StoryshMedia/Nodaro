<?php

namespace Smug\Core\Command\Install;

use Smug\Core\Context\Context;
use Smug\Core\DataAbstractionLayer\EntityGenerator;
use Smug\Core\Entity\Base\BaseModel;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\SystemBundle\Entity\Country\Country;
use Smug\SystemBundle\Entity\Language\Language;
use Smug\SystemBundle\Entity\Permission\Permission;
use Smug\SystemBundle\Entity\User\User;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DatabaseBuildCommand extends Command
{
    private Context $context;

    protected UserPasswordHasherInterface $hasher;

    public function __construct(KernelInterface $kernel, Context $context, UserPasswordHasherInterface $hasher)
    {
        $this->context = $context;
        $this->hasher = $hasher;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('nodaro:install:database')
            ->setDescription('installs base database data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (DataHandler::doesFileExist(__DIR__ . '/installed')) {
            return 0;
        }

        $userLanguage = null;

        $countryClass = EntityGenerator::getGeneratedEntity(Country::class);
        $languageClass = EntityGenerator::getGeneratedEntity(Language::class);
        $userGroupClass = EntityGenerator::getGeneratedEntity(UserGroup::class);
        $userClass = EntityGenerator::getGeneratedEntity(User::class);
        $permissionClass = EntityGenerator::getGeneratedEntity(Permission::class);

        foreach (\Smug\Core\Command\Install\Data\Country::getCountries() as $countryData) {
            $country = new $countryClass();
            $country->__set('title', $countryData['title']);
            $country->__set('token', $countryData['token']);
            $country->__set('defaultCountry', $countryData['defaultCountry']);

            $this->context->getEntityManager()->persist($country);
            $this->context->getEntityManager()->flush();
        }

        foreach (\Smug\Core\Command\Install\Data\Language::getLanguages() as $languageData) {
            $language = new $languageClass();
            $language->__set('title', $languageData['title']);
            $language->__set('locale', $languageData['locale']);
            $language->__set('area', $languageData['area']);
            $language->__set('translationAvailable', $languageData['translationAvailable']);

            $this->context->getEntityManager()->persist($language);
            $this->context->getEntityManager()->flush();
            if ($languageData['locale'] === 'en') {
                $userLanguage = $language;
            }
        }

        $userGroup = new $userGroupClass();
        $userGroup->__set('title', 'Administratoren');
        $userGroup->__set('description', 'Administratoren => Alle Rechte');
        $userGroup->__set('admin', true);

        $this->context->getEntityManager()->persist($userGroup);
        $this->context->getEntityManager()->flush();

        $metas = $this->context->getEntityManager()->getMetadataFactory()->getAllMetadata();

        foreach ($metas as $meta) {
            $class = $meta->getName();
            if ($class === BaseModel::class) {
                continue;
            }
            $permission = new $permissionClass();
            $modelArray = DataHandler::explodeArray('\\', $class);

            $model = DataHandler::getLastArrayElement($modelArray);

            $permission->__set('userGroup', $userGroup);
            $permission->__set('modelClass', $class);
            $permission->__set('model', $model);
            $permission->__set('canRead', false);
            $permission->__set('canWrite', false);
            $permission->__set('disallowedFields', '');
            $permission->__set('hiddenFields', '');
            $permission->__set(
                'type',
                DataHandler::getReplaceString('Bundle', '', $modelArray[1])
            );

            $this->context->getEntityManager()->persist($permission);
            $this->context->getEntityManager()->flush();
        }

        $user = new $userClass();

        $user->__set('language', $userLanguage);
        $user->__set('name', 'admin');
        $user->__set('surname', 'admin');
        $user->__set('username', 'admin');
        $user->__set('usernameCanonical', 'admin');
        $user->__set('password', 'admin');
        $user->__set('email', 'info@example.com');
        $user->__set('emailCanonical', 'info@example.com');
        $user->__set('enabled', true);
        $user->__set('lastLogin', null);
        $user->__set('confirmationToken', null);
        $user->__set('passwordRequestedAt', null);
        $user->__set('userGroup', $userGroup);
        $user->__set('roles', 'a:1:{i:0;s:16:"ROLE_SUPER_ADMIN";}');
        
        $this->context->getEntityManager()->persist($user);
        $this->context->getEntityManager()->flush();
        
        $user->__set('password', $this->hasher->hashPassword($user, 'admin'));

        $this->context->getEntityManager()->persist($user);
        $this->context->getEntityManager()->flush();

        DataHandler::writeFile(__DIR__ . '/installed', '');
	    
        return 0;
    }
}
