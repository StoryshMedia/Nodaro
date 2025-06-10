<?php

namespace Smug\FrontendBundle\Constants\Config\Backend;

use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;

class ContentItemModuleFieldConstants
{
	const MAPPING = [
		'returnObject' => false,
		'mapValues' => [
			[
				'identifier' => 'module',
				'class' => ContentItemModule::class
			]
		]
	];
}
