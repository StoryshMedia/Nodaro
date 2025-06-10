<?php

namespace Smug\FrontendBundle\Constants\Config\Backend;

use Smug\FrontendBundle\Entity\Domain\Domain;
use Smug\FrontendBundle\Entity\Script\Script;
use Smug\FrontendBundle\Entity\Site\Site;

class SiteScriptConstants
{
	const MAPPING = [
		'returnObject' => false,
		'mapValues' => [
			[
				'identifier' => 'site',
				'class' => Site::class
			],
			[
				'identifier' => 'script',
				'class' => Script::class
			]
		]
	];
}
