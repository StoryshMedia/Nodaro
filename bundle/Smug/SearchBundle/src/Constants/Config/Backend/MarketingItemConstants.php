<?php

namespace Smug\SearchBundle\Constants\Config\Backend;

use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;

class MarketingItemConstants
{
	const MAPPING = [
		'returnObject' => false,
		'mapValues' => [
			[
				'identifier' => 'searchWindow',
				'class' => SearchWindow::class
			]
		]
	];
}
