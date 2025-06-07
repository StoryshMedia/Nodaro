<?php

namespace Smug\SearchBundle\Tests\Service\Search;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Smug\SearchBundle\Service\Search\Listing\ListService;
use Smug\Core\Service\Base\Components\Handler\DataHandler;

class ListServiceTest extends KernelTestCase
{
	protected $container;

	private static $authorArray = [
		'firstName' => [
			'type' => 'string'
		],
		'lastName' => [
			'type' => 'string'
		],
		'image' => [
			'type' => 'array'
		],
		'additional' => [
			'type' => 'array'
		],
		'path' => [
			'type' => 'string'
		],
		'slug' => [
			'type' => 'string'
		],
		'score' => [
			'type' => 'double'
		],
		'label' => [
			'type' => 'string'
		]
	];

	private static $publicationArray = [
		'title' => [
			'type' => 'string'
		],
		'subTitle' => [
			'type' => 'string'
		],
		'image' => [
			'type' => 'array'
		],
		'additional' => [
			'type' => 'array'
		],
		'path' => [
			'type' => 'string'
		],
		'slug' => [
			'type' => 'string'
		],
		'score' => [
			'type' => 'double'
		],
		'teaser' => [
			'type' => 'string'
		],
		'summary' => [
			'type' => 'string'
		],
		'label' => [
			'type' => 'string'
		]
	];

	private static $userArray = [
		'firstName' => [
			'type' => 'string'
		],
		'lastName' => [
			'type' => 'string'
		],
		'image' => [
			'type' => 'array'
		],
		'additional' => [
			'type' => 'array'
		],
		'path' => [
			'type' => 'string'
		],
		'slug' => [
			'type' => 'string'
		],
		'score' => [
			'type' => 'double'
		],
		'label' => [
			'type' => 'string'
		]
	];

	private static $marketArray = [
		'title' => [
			'type' => 'string'
		],
		'image' => [
			'type' => 'array'
		],
		'path' => [
			'type' => 'string'
		],
		'slug' => [
			'type' => 'string'
		],
		'score' => [
			'type' => 'double'
		]
	];

	private static $storyArray = [
		'title' => [
			'type' => 'string'
		],
		'subTitle' => [
			'type' => 'string'
		],
		'label' => [
			'type' => 'string'
		],
		'image' => [
			'type' => 'array'
		],
		'path' => [
			'type' => 'string'
		],
		'slug' => [
			'type' => 'string'
		],
		'score' => [
			'type' => 'double'
		]
	];

	protected function setUp(): void
    {
        self::bootKernel([
			"environment" => 'test'
		  ]);
        $this->container = static::getContainer();
    }

    public function testGetSearchAuthors()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchAuthors('Chris Carter');

		foreach (self::$authorArray as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, $data['results'][0]));
			$this->assertEquals(DataHandler::getVariableType($data['results'][0][$key]), $value['type']);
		}
    }

    public function testGetSearchAuthorsReverse()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchAuthors('Chris Carter');

		foreach ($data['results'][0] as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, self::$authorArray));
			$this->assertEquals(DataHandler::getVariableType($value), self::$authorArray[$key]['type']);
		}
    }

    public function testGetSearchUsers()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchAuthors('Alex');

		foreach (self::$userArray as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, $data['results'][0]));
			$this->assertEquals(DataHandler::getVariableType($data['results'][0][$key]), $value['type']);
		}
    }

    public function testGetSearchUsersReverse()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchAuthors('Alex');

		foreach ($data['results'][0] as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, self::$userArray));
			$this->assertEquals(DataHandler::getVariableType($value), self::$userArray[$key]['type']);
		}
    }

    public function testGetSearchMarkets()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchMarkets('Beutegier');

		foreach (self::$marketArray as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, $data['results'][0]));
			$this->assertEquals(DataHandler::getVariableType($data['results'][0][$key]), $value['type']);
		}
    }

    public function testGetSearchMarketsReverse()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchMarkets('Beutegier');

		foreach ($data['results'][0] as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, self::$marketArray));
			$this->assertEquals(DataHandler::getVariableType($value), self::$marketArray[$key]['type']);
		}
    }

    public function testGetSearchPublications()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchPublications('Beutegier');

		foreach (self::$publicationArray as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, $data['results'][0]));
			$this->assertEquals(DataHandler::getVariableType($data['results'][0][$key]), $value['type']);
		}
    }

    public function testGetSearchPublicationsReverse()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchPublications('Beutegier');

		foreach ($data['results'][0] as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, self::$publicationArray));
			$this->assertEquals(DataHandler::getVariableType($value), self::$publicationArray[$key]['type']);
		}
    }

    public function testGetSearchStories()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchStories('Zusammen');

		foreach (self::$storyArray as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, $data['results'][0]));
			$this->assertEquals(DataHandler::getVariableType($data['results'][0][$key]), $value['type']);
		}
    }

    public function testGetSearchStoriesReverse()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearchStories('Zusammen');

		foreach ($data['results'][0] as $key => $value) {
			$this->assertTrue(DataHandler::doesKeyExists($key, self::$storyArray));
			$this->assertEquals(DataHandler::getVariableType($value), self::$storyArray[$key]['type']);
		}
    }

    public function testGetSearch()
    {
		$listService = $this->container->get(ListService::class);
		$data = $listService->getSearch('Zusammen');

		foreach ($data as $value) {
			$this->assertTrue(DataHandler::doesKeyExists('results', $value));
			$this->assertTrue(DataHandler::isArray($value['results']));
		}
    }
}

