<?php

namespace Smug\SystemBundle\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;

class SettingsConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [];

	const DETAIL_DATA = [
		'config' => [],
		'tabs' => [
			[
				'headline' => 'BASE_SETTINGS',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => [
							[
								'type' => 'Infobox',
								'config' => [
									'icon' => 'IconMenuChat',
									'headline' => 'LANGUAGES',
									'text' => 'LANGUAGES_INFO_TEXT',
									'linkText' => 'TO_LANGUAGES',
									'linkUrl' => '/admin/smug/system/language/list',
								]
							],
							[
								'type' => 'Infobox',
								'config' => [
									'icon' => 'IconUsersGroup',
									'headline' => 'USER_GROUPS',
									'text' => 'SECURITY_INFO_TEXT',
									'linkText' => 'TO_GROUPS',
									'linkUrl' => '/admin/smug/system/user_group/list',
								]
							]
						]
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
		return [];
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
