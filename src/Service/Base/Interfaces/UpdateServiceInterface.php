<?php

namespace Smug\Core\Service\Base\Interfaces;

use Smug\Core\Context\Context;

interface UpdateServiceInterface
{
	/**
	 * @param array $data
	 * @return array
	 */
	public function save(Context $context): array;
	
	/**
	 * @param array $data
	 * @return array
	 */
	public function delete(Context $context): array;

    /**
     * @param array $data
     * @param boolean $import
     * @return array
     */
    public function quickEdit(array $data, $import = false): array;
}
