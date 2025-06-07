<?php

namespace Smug\AdministrationBundle\Interface\Navigation;

use Smug\SystemBundle\Entity\UserGroup\UserGroup;

interface NavigationBuilderInterface {
    public static function collect(UserGroup $userGroup): array;

    public static function transform(array $configuration, UserGroup $userGroup): array;
}