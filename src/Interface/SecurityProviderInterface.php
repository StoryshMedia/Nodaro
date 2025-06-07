<?php

namespace Smug\Core\Interface;

use Smug\SystemBundle\Entity\Permission\Permission;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;

interface SecurityProviderInterface
{
	public static function getModelPermissions(string $model, $userGroup);
}
