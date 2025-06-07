<?php

namespace Smug\SystemBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\SystemBundle\Entity\Language\Language;

class LanguageConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [
		'config' => [
			'columns' => [
				[
					'identifier' => 'title',
					'type' => 'string'
				],
				[
					'identifier' => 'locale',
					'type' => 'string'
				]
			],
			'type' => 'table',
			'model' => Language::class,
			'listConfig' => [
				'showLogo' => false,
				'url' => [
					'detail' => true,
					'add' => true,
					'backUrl' => 'smug/system/settings/detail',
				],
				'paginatorModel' => 'languages',
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
			'model' => Language::class,
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
						'class' => 'grid grid-cols-1 md:grid-cols-4 gap-5 my-5',
						'fields' => ['title', 'locale', 'area', 'translationAvailable']
					]
				]
			]
		]
	];

	const DETAIL_DATA = [
		'config' => [
			'model' => Language::class,
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
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => ['id']
					],
					[
						'class' => 'grid grid-cols-1 md:grid-cols-4 gap-5 my-5',
						'fields' => ['title', 'locale', 'area', 'translationAvailable']
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
