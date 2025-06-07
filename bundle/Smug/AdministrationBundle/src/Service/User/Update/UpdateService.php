<?php

namespace Smug\AdministrationBundle\Service\User\Update;

use Smug\Core\Batch\User\User\UserBatch;
use Smug\Core\Entity\Dashboard\Dashboard\Dashboard;
use Smug\Core\Entity\Finance\Collection\CollectionComment;
use Smug\Core\Entity\System\Company\Company;
use Smug\SystemBundle\Entity\Language\Language;
use Smug\Core\Entity\System\Setting\Settings;
use Smug\SystemBundle\Entity\User\User;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;
use Smug\SystemBundle\Preparer\User\UserPreparer;
use Smug\SystemBundle\Validator\User\UserValidator;
use Smug\Core\Exception\Base\NotValidException;
use Smug\Core\Service\Base\Components\Processor\RemoveProcessor;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ExceptionProvider;
use Smug\Core\Service\Base\Factory\ServiceGenerationFactory;
use Smug\Core\Service\Base\Service\UpdateBaseService;
use Doctrine\DBAL\ConnectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use \Exception;
use Smug\Core\Context\Context;

class UpdateService extends UpdateBaseService
{
    /**
     * @inheritDoc
     */
    public function save(Context $context): array
    {
        try {
        	/** @var UserPreparer $preparer */
            $preparer = ServiceGenerationFactory::createInstance(UserPreparer::class);
            /** @var UserValidator $validator */
            $validator = ServiceGenerationFactory::createInstance(UserValidator::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        $backupUser = $data;

        /** @var User $userObj */
        $userObj = $this->em->getRepository(User::class)->findOneBy(['id' => $data['id']]);
        /** @var UserGroup $userGroup */
        $userGroup = $this->mapValue($data, UserGroup::class, false, 'userGroup');
        /** @var Language $language */
        $language = $this->mapValue($data, Language::class, false, 'language');
	    
        try {
	        $user = $preparer->prepare($data,
		        $this->getContainerVariable('security.password_encoder'),
		        null,
		        $language,
		        $userGroup
	        );
	        
	        $user = $validator->validate($user);
        } catch (NotValidException $exception) {
            return ExceptionProvider::getException($exception);
        }

        $companies = $this->getEntitiesFromSelectionList(
            ArrayProvider::getObjectsAsArray($userObj->getCompanies()), $backupUser['companies']
        );

        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->update(User::class, 'u')
            ->set('u.username', ':name')
            ->set('u.usernameCanonical', ':nameCanonical')
            ->set('u.emailCanonical', ':emailCanonical')
            ->set('u.email', ':email')
            ->set('u.language', ':language')
            ->set('u.userGroup', ':userGroup')
            ->set('u.name', ':firstName')
            ->set('u.surname', ':lastName')
            ->set('u.approvalMaximum', ':approvalMaximum')
            ->where('u.id = :userId')
            ->setParameter('name', $user['name'])
            ->setParameter('nameCanonical', $user['nameCanonical'])
            ->setParameter('emailCanonical', $user['emailCanonical'])
            ->setParameter('email', $user['email'])
            ->setParameter('language', $user['language'])
            ->setParameter('userGroup', $user['userGroup'])
            ->setParameter('firstName', $user['firstName'])
            ->setParameter('lastName', $user['lastName'])
            ->setParameter('approvalMaximum', $user['approvalMaximum'])
            ->setParameter('userId', $user['id'])
            ->getQuery()
            ->execute();

        $this->handleManyToManyAssociations(
            $userObj,
            $companies,
            [
                'associationClass' => Company::class,
                'addFunction' => 'addCompany',
                'removeFunction' => 'removeCompany'
            ]
        );

        return [
            'success' => true,
            'data' => $userObj->toArray()
        ];
    }

    /**
     * @param array $users
     * @return array
     */
    public function deleteUser(array $users): array
    {
        $userRepository = $this->em->getRepository(User::class);

        foreach ($users as $user) {
            /** @var User $user */
            $user = $userRepository->findOneBy(['id' => $user['id']]);

            $this->resetDeleteUserDependencies($user);

            RemoveProcessor::process($this->em, $this->setter, null, '', [], null, false, $user);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param array $data
     * @param false $reset
     * @return array|bool[]
     */
    public function changePassword(array $data, $reset = false): array
    {
        try {
            /** @var UserPreparer $preparer */
            $preparer = ServiceGenerationFactory::createInstance(UserPreparer::class);
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        /** @var UserPasswordEncoder $encoder */
        $encoder = $this->getContainerVariable('security.password_encoder');

        if (null === $this->currentUser) {
            return [
                'status' => JsonResponse::HTTP_FORBIDDEN,
                'message' => 'User not found'
            ];
        }

        if ($reset === false) {
            /* Checking current user password with given password param from $reqest */
            $passwordValid = $encoder->isPasswordValid($this->currentUser, $data['oldPassword']);

            if (!$passwordValid) {
                return [
                    'status' => JsonResponse::HTTP_FORBIDDEN,
                    'message' => 'Mismatch current password'
                ];
            }
        }

        $this->currentUser = $preparer->preparePasswordChange($this->currentUser);
        $this->currentUser->setPassword($encoder->encodePassword($this->currentUser, $data['newPassword']));

        $queryBuilder = $this->em->createQueryBuilder();

        $queryBuilder->update(User::class, 'u')
            ->set('u.password', ':password')
            ->where('u.id = :userId')
            ->setParameter('password', $this->currentUser->getPassword())
            ->setParameter('userId', $this->currentUser->getId())
            ->getQuery()
            ->execute();

        return [
            'success' => true
        ];
    }

    /**
     * @param array $data
     * @param UserBatch $batch
     * @return array
     * @throws ConnectionException
     */
    public function batchProcess(array $data, UserBatch $batch): array
    {
        return $this->processBatch(
            [
                'identifier' => 'tasks',
                'data' => $data,
                'class' => User::class
            ],
            $batch
        );
    }

    /**
     * @param User $user
     * @return array
     */
    public function resetDeleteUserDependencies(User $user): array
    {
        try {
            $this->resetProjectDependencies($user);
            $this->resetSupportDependencies($user);
            $this->resetCampaignDependencies($user);
            $this->resetDashboardDependencies($user);
            $this->resetSystemSettingDependencies($user);
            $this->resetTaskDependencies($user);
            $this->resetCustomerDependencies($user);
            $this->resetFinanceDependencies($user);

        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetFinanceDependencies(User $user): array
    {
        try {
            $collectionCommentRepository = $this->em->getRepository(CollectionComment::class);

            $comments = $collectionCommentRepository->findBy(['author' => $user->getId()]);

            /** @var CollectionComment $comment */
            foreach ($comments as $comment) {
                $comment->setAuthor();
                $this->em->persist($comment);
                $this->em->flush();
            }

            $this->em->flush();
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetCustomerDependencies(User $user): array
    {
        try {
            $leadCustomerCommentRepository = $this->em->getRepository(LeadCustomerComment::class);
            $customerCommentRepository = $this->em->getRepository(CustomerComment::class);
            $customerContactCommentRepository = $this->em->getRepository(ContactComment::class);

            $customerComments = $customerCommentRepository->findBy(['author' => $user->getId()]);
            $contactComments = $customerContactCommentRepository->findBy(['author' => $user->getId()]);
            $leadComments = $leadCustomerCommentRepository->findBy(['author' => $user->getId()]);

            /** @var CustomerComment $comment */
            foreach ($customerComments as $comment) {
                $comment->setAuthor();
                $this->em->persist($comment);
                $this->em->flush();
            }

            /** @var ContactComment $comment */
            foreach ($contactComments as $comment) {
                $comment->setAuthor();
                $this->em->persist($comment);
                $this->em->flush();
            }

            /** @var LeadCustomerComment $comment */
            foreach ($leadComments as $comment) {
                $comment->setAuthor();
                $this->em->persist($comment);
                $this->em->flush();
            }
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetTaskDependencies(User $user): array
    {
        try {
            $taskRepository = $this->em->getRepository(Task::class);
            $taskNoticeRepository = $this->em->getRepository(Notice::class);

            $tasks = $taskRepository->findBy(['recipient' => $user->getId()]);
            $notices = $taskNoticeRepository->findBy(['author' => $user->getId()]);

            /** @var Task $task */
            foreach ($tasks as $task) {
                $task->setRecipient();
                $this->em->persist($task);
                $this->em->flush();
            }

            /** @var Notice $notice */
            foreach ($notices as $notice) {
                $notice->setAuthor();
                $this->em->persist($notice);
                $this->em->flush();
            }
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetSystemSettingDependencies(User $user): array
    {
        try {
            $systemRepository = $this->em->getRepository(Settings::class);

            $settings = $systemRepository->findBy(['user' => $user->getId()]);

            /** @var Settings $setting */
            foreach ($settings as $setting) {
                $setting->setUser();
                $this->em->persist($setting);
                $this->em->flush();

                $this->em->remove($setting);
            }

            $this->em->flush();
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetDashboardDependencies(User $user): array
    {
        try {
            $dashboardRepository = $this->em->getRepository(Dashboard::class);

            $dashboards = $dashboardRepository->findBy(['user' => $user->getId()]);

            /** @var Dashboard $dashboard */
            foreach ($dashboards as $dashboard) {
                $this->em->remove($dashboard);
            }

            $this->em->flush();
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetCampaignDependencies(User $user): array
    {
        try {
            $campaignRepository = $this->em->getRepository(Campaign::class);

            $campaigns = $campaignRepository->findBy(['manager' => $user->getId()]);

            /** @var Campaign $campaign */
            foreach ($campaigns as $campaign) {
                $campaign->setManager();
                $this->em->persist($campaign);
            }

            $cardRepository = $this->em->getRepository(Card::class);

            $cards = $cardRepository->findBy(['manager' => $user->getId()]);

            /** @var Card $card */
            foreach ($cards as $card) {
                $card->setManager();
                $this->em->persist($card);
            }

            $recipientRepository = $this->em->getRepository(Recipient::class);

            $recipients = $recipientRepository->findBy(['recipient' => $user->getId()]);

            /** @var Recipient $recipient */
            foreach ($recipients as $recipient) {
                $recipient->setRecipient();
                $this->em->persist($recipient);
            }

            $noticeRepository = $this->em->getRepository(\Smug\Core\Entity\Marketing\Campaign\Card\Notice::class);

            $notices = $noticeRepository->findBy(['reporter' => $user->getId()]);

            /** @var \Smug\Core\Entity\Marketing\Campaign\Card\Notice $notice */
            foreach ($notices as $notice) {
                $notice->setAuthor();
                $this->em->persist($notice);
            }

            $this->em->flush();
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetSupportDependencies(User $user): array
    {
        try {
            $noticeRepository = $this->em->getRepository(\Smug\Core\Entity\Communication\Support\Notice::class);

            $notices = $noticeRepository->findBy(['author' => $user->getId()]);

            /** @var \Smug\Core\Entity\Communication\Support\Notice $notice */
            foreach ($notices as $notice) {
                $notice->setAuthor();
                $this->em->persist($notice);

                $this->em->flush();
            }

            $ticketRepository = $this->em->getRepository(Tickets::class);

            $tickets = $ticketRepository->findBy(['agent' => $user->getId()]);

            /** @var Tickets $privateTicket */
            foreach ($tickets as $privateTicket) {
                $privateTicket->setAgent();
                $this->em->persist($privateTicket);

                $this->em->flush();
            }
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    private function resetProjectDependencies(User $user): array
    {
        try {
            $projectRepository = $this->em->getRepository(Project::class);

            $managerProjects = $projectRepository->findBy(['manager' => $user->getId()]);

            /** @var Project $managerProject */
            foreach ($managerProjects as $managerProject) {
                $managerProject->setManager();
                $this->em->persist($managerProject);
            }

            $deputyProjects = $projectRepository->findBy(['deputy' => $user->getId()]);

            /** @var Project $deputyProject */
            foreach ($deputyProjects as $deputyProject) {
                $deputyProject->setDeputy();
                $this->em->persist($deputyProject);
            }

            $cardRepository = $this->em->getRepository(Card::class);

            $cards = $cardRepository->findBy(['manager' => $user->getId()]);

            /** @var Card $card */
            foreach ($cards as $card) {
                $card->setManager();
                $this->em->persist($card);
            }

            $recipientRepository = $this->em->getRepository(\Smug\Core\Entity\Project\Card\Recipient::class);

            $recipients = $recipientRepository->findBy(['recipient' => $user->getId()]);

            /** @var \Smug\Core\Entity\Project\Card\Recipient $recipient */
            foreach ($recipients as $recipient) {
                $recipient->setRecipient();
                $this->em->persist($recipient);
            }

            $noticeRepository = $this->em->getRepository(\Smug\Core\Entity\Project\Card\Notice::class);

            $notices = $noticeRepository->findBy(['reporter' => $user->getId()]);

            /** @var \Smug\Core\Entity\Project\Card\Notice $notice */
            foreach ($notices as $notice) {
                $notice->setAuthor();
                $this->em->persist($notice);
            }

            $this->em->flush();
        } catch (Exception $exception) {
            return ExceptionProvider::getException($exception);
        }

        return [
            'success' => true
        ];
    }
}
