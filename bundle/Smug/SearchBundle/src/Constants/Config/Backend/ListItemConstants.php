<?php

namespace Smug\SearchBundle\Constants\Config\Backend;

use Smug\SearchBundle\Entity\SearchWindow\SearchWindow;

class ListItemConstants
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
