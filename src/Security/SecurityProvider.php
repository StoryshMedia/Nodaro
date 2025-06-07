<?php

namespace Smug\Core\Security;

use Smug\Core\Interface\SecurityProviderInterface;
use Smug\Core\Service\Base\Components\Provider\DataProvider\ArrayProvider;

class SecurityProvider implements SecurityProviderInterface
{
	public static function getModelPermissions(string $model, $userGroup)
  {
		return ArrayProvider::getObjectFromChildItem($userGroup->__get('permission'), 'model', $model);
  }

	public static function getModelPermissionsFromClass(string $class, $userGroup)
  {
		return ArrayProvider::getObjectFromChildItem($userGroup->__get('permission'), 'modelClass', $class);
  }
}
