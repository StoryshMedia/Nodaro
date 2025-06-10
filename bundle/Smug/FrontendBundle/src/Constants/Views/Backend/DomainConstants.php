<?php

namespace Smug\FrontendBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\FrontendBundle\Entity\Domain\Domain;

class DomainConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [
		'config' => [
			'columns' => [
				[
					'identifier' => 'title',
					'type' => 'string'
				],
				[
					'identifier' => 'url',
					'type' => 'string'
				]
			],
			'type' => 'table',
			'model' => Domain::class,
			'listConfig' => [
				'showLogo' => false,
				'url' => [
					'detail' => true,
					'add' => true
				],
				'paginatorModel' => 'domains',
				'deleteSelected' => true,
				'batchProcessing' => false,
				'paginationLimits' => [5, 10, 20, 50, 100]
			],
			'sortings' => [],
			'filters' => []
		]
	];

	const DETAIL_DATA = [
		'config' => [
			'model' => Domain::class,
			'url' => [
				'save' => true
			],
			'processEvent' => 'domainSaved'
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => ["title", "url", "templateString"]
					]
				]
			],
			[
				'headline' => 'SEO_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-3 gap-5 my-5',
						'fields' => [
							"seo.title",
							"seo.images",
							[
								'type' => 'FileUpload',
								'placeholder' => 'IMAGE_UPLOAD',
								'identifier' => 'seo.images',
								'config' => [
									'uploadCall' => '/be/api/media/image/upload',
									'assignCall' => '/be/api/smug/frontend/seo/images/assign',
									'multiple' => true,
									'mini' => true,
									'dropText' => 'DROP_HERE',
									'assignAlbum' => 'domain',
									'baseModel' => [],
									'bypassId' => false,
									'bypassSubId' => true
								]
							]
						]
					]
				]
			],
			[
				'headline' => 'SITE_TREE',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1',
						'fields' => ["sites"]
					]
				]
			]
		]
	];

	const ADD_DATA = [
		'config' => [
			'model' => Domain::class,
			'url' => [
				'save' => true
			],
			'processEvent' => 'domainAdded'
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => ["title", "url"]
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
