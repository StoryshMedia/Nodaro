<?php

namespace Smug\SystemBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;
use Smug\SystemBundle\Entity\User\User;

class ProfileConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [];

	const ADD_DATA = [];

	const DETAIL_DATA = [
		'config' => [
			'model' => User::class,
			'url' => [
				'customGet' => '/be/api/custom/profile',
				'save' => true
			],
		],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 mb-12',
						'fields' => [
							'images',
							[
								'type' => 'FileUpload',
								'placeholder' => 'IMAGE_UPLOAD',
								'identifier' => 'images',
								'config' => [
									'uploadCall' => '/be/api/media/image/upload',
									'multiple' => true,
									'dropText' => 'DROP_HERE',
									'assignAlbum' => 'user',
									'baseModel' => [],
									'bypassId' => true
								]
							]
						]
					],
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
						'fields' => ['language']
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
