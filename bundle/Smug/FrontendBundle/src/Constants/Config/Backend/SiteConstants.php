<?php

namespace Smug\FrontendBundle\Constants\Config\Backend;

use Smug\FrontendBundle\Entity\ContentItemModule\ContentItemModule;
use Smug\FrontendBundle\Entity\Domain\Domain;

class SiteConstants
{
	const MAPPING = [
		'returnObject' => false,
		'mapValues' => [
			[
				'identifier' => 'module',
				'class' => ContentItemModule::class
			],
			[
				'identifier' => 'domain',
				'class' => Domain::class
			]
		]
	];
}
