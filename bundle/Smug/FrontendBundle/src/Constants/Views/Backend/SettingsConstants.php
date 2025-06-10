<?php

namespace Smug\FrontendBundle\Constants\Views\Backend;

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
									'headline' => 'FRONTEND_MODULES',
									'text' => 'FRONTEND_MODULES_INFO_TEXT',
									'linkText' => 'TO_MODULES',
									'linkUrl' => '/admin/smug/frontend/module/detail',
								]
							],
							[
								'type' => 'Infobox',
								'config' => [
									'icon' => 'IconPaper',
									'headline' => 'SITE_SCRIPTS',
									'text' => 'SITE_SCRIPTS_INFO_TEXT',
									'linkText' => 'TO_SCRIPTS',
									'linkUrl' => '/admin/smug/frontend/script/detail',
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
