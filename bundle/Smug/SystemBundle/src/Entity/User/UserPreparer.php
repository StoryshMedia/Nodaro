<?php

namespace Smug\SystemBundle\Entity\User;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Query\QueryMapper;

class UserPreparer extends QueryMapper
{
    public function prepare(array $user, array $mapValues): array
    {
        return DataHandler::mergeArray($user, $mapValues);
    }
}
