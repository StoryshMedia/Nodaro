<?php

namespace Smug\Core\Service\Base\Interfaces;

use Smug\Core\Context\Context;

/**
 * Interface ListServiceInterface
 * @package Smug\Core\Service\Base\Interfaces
 */
interface ListServiceInterface
{
	/**
	 * @param $id
	 * @return array
	 */
	public function getSingle(Context $context): array;
	
	/**
	 * @return array
	 */
	public function getData(Context $context): array;
	
	/**
	 * @param array $params
	 * @return array
	 */
	public function getPaginated(Context $context): array;
	
	/**
	 * @param Context $context
	 * @return array
	 */
	public function getSubData(Context $context): array;
	
	/**
	 * @param bool $onlyNames
	 * @return array
	 */
	public function getFields($onlyNames = false): array;

    /**
     * @param string $queryString
     * @param int $maxResults
     * @return array
     */
	public function getSearch(string $queryString, int $maxResults = 3): array;
	
	/**
	 * @param string $id
	 * @return array
	 */
	public function getSearchResultDetails(string $id): array;
}
