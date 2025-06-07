<?php

namespace Smug\SystemBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\SystemBundle\Entity\UserGroup\UserGroup;

class UserGroupConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [
		'config' => [
			'columns' => [
				[
					'identifier' => 'title',
					'type' => 'string'
				],
				[
					'identifier' => 'description',
					'type' => 'string'
				]
			],
			'type' => 'table',
			'model' => UserGroup::class,
			'listConfig' => [
				'showLogo' => false,
				'url' => [
					'detail' => true,
					'add' => true
				],
				'paginatorModel' => 'groups',
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
			'model' => UserGroup::class,
			'url' => [
				'save' => true
			],
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => ['title', 'admin']
					],
					[
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => ['description']
					]
				]
			]
		]
	];

	const DETAIL_DATA = [
		'config' => [
			'model' => UserGroup::class,
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
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => ['id']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => ['title', 'admin']
					],
					[
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => ['description']
					]
				]
			],
			[
				'headline' => 'MEMBERS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => ['member']
					]
				]
			],
			[
				'headline' => 'PERMISSIONS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
						'fields' => [
							[
								'type' => 'Button',
								'placeholder' => '',
								'config' => [
									'buttonText' => 'RENEW',
									'buttonType' => 'dark',
									'method' => 'POST',
									'functionCall' => '/be/api/custom/permission/renew',
									'bypassId' => true
								]
							]
						]
					],
					[
						'class' => 'grid grid-cols-1 gap-5 my-5',
						'fields' => ['permission']
					]
				]
			]
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
