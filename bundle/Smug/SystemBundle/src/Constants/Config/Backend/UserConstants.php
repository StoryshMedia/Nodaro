<?php

namespace Smug\SystemBundle\Constants\Config\Backend;

use Smug\SystemBundle\Entity\Language\Language;
use Smug\SystemBundle\Entity\Media\MediaUserAssociation;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;

class UserConstants
{
	const PAGINATION_CONFIG = [
		'model' => 'users',
		'titleIdentifier' => 'username',
		'fields' => ['username']
	];

	const MAPPING = [
		'returnObject' => false,
		'mapValues' => [
			[
				'identifier' => 'userGroup',
				'class' => UserGroup::class
			],
			[
				'identifier' => 'language',
				'class' => Language::class
			]
		],
		'media' => [
			'class' => MediaUserAssociation::class,
			'removeExisting' => false,
			'modelIdentifier' => 'user'
		],
	];
}
