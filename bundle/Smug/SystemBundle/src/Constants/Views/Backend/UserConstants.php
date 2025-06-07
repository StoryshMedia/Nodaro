<?php

namespace Smug\SystemBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\SystemBundle\Entity\User\User;

class UserConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [
		'config' => [
			'columns' => [
				[
					'identifier' => 'username',
					'type' => 'string'
				],
				[
					'identifier' => 'name',
					'type' => 'string'
				],
				[
					'identifier' => 'surname',
					'type' => 'string'
				]
			],
			'type' => 'table',
			'model' => User::class,
			'listConfig' => [
				'showLogo' => false,
				'url' => [
					'detail' => true,
					'add' => true
				],
				'paginatorModel' => 'users',
				'zone' => 'settings',
				'deleteSelected' => true,
				'batchProcessing' => false,
				'paginationLimits' => [5, 10, 20, 50, 100]
			],
			'sortings' => [],
			'filters' => []
		]
	];

	const ADD_DATA = [
		'config' => [
			'model' => User::class,
			'url' => [
				'save' => true,
				'delete' => true
			],
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-3 gap-5 mb-12',
						'fields' => ['username', 'name', 'surname']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
						'fields' => ['email', 'password']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
						'fields' => ['language', 'userGroup']
					],
					[
						'class' => 'grid grid-cols-1 gap-5 mb-12',
						'fields' => ['enabled']
					]
				]
			]
		]
	];

	const DETAIL_DATA = [
		'config' => [
			'model' => User::class,
			'url' => [
				'save' => true,
				'delete' => true
			],
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-3 gap-5 mb-12',
						'fields' => ['username', 'name', 'surname']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
						'fields' => ['email', 'password']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-3 gap-5 mb-12',
						'fields' => ['language', 'userGroup', 'lastLogin']
					],
					[
						'class' => 'grid grid-cols-1 gap-5 mb-12',
						'fields' => ['enabled']
					]
				]
			],
		]
	];

	public static function getListConstants(): array
	{
		return self::LIST_DATA;
	}

	public static function getDetailConstants(): array
	{
		return self::DETAIL_DATA;
	}

	public static function getAddConstants(): array
	{
		return self::ADD_DATA;
	}

	public static function getReadingRights(): string
	{
		return '';
	}

	public static function getWritingRights(): string
	{
		return '';
	}
}
