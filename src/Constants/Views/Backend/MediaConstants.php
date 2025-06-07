<?php

namespace Smug\Core\Constants\Views\Backend;

use Smug\Core\Service\Base\Interfaces\Backend\BackendDataConstantsInterface;

class MediaConstants implements BackendDataConstantsInterface
{
	const LIST_DATA = [];

	const DETAIL_DATA = [
		'config' => [],
		'tabs' => [
			[
				'headline' => 'MEDIA',
				'type' => 'standard',
				'rows' => [
					[
						'class' => 'grid grid-cols-1 md:grid-cols-2 gap-5 my-5',
						'fields' => [
							[
								'type' => 'MediaCenter',
								'config' => []
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
}
