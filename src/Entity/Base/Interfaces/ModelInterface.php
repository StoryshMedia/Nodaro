<?php

namespace Smug\Core\Entity\Base\Interfaces;

/**
 * Interface ModelInterface
 * @package Smug\Core\Entity\Base\Interfaces
 */
interface ModelInterface
{
    /** @return string */
    public function getId();

    /**
     * @param string $id
     */
    public function setId(string $id): void;

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return array
     */
    public function getListItem();

    /**
     * @param array $fields
     * @return array
     */
    public function getSelectedFieldsItem(array $fields);

    /**
     * @return array
     */
    public function getPlainArray();

    /**
     * @param array $entries
     * @param string $parentId
     * @return array
     */
    public function getTree(array $entries, $parentId = '');
}
