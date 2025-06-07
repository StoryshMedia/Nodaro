<?php

namespace Smug\AdministrationBundle\Service\User\Listing;

use Smug\SystemBundle\Entity\User\User;
use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Service\ListBaseService;

class ListService extends ListBaseService
{
    public function getUserFeeds(): array
    {
        $users = [];

        /** @var  User $user */
        foreach ($this->em->getRepository(User::class)->findAll() as $key => $user) {
              $users[] = $user->getFeedData();
        }

        return $users;
    }

    public function getUserEmailFromMention(string $mention): string
    {
        $userName = DataHandler::getReplaceString('@', '', $mention);

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $userName]);

        return $user->getEmail();
    }
}
