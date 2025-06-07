<?php

namespace Smug\Core\Service\Base\Components\Writer;

/**
 * Interface WriterInterface
 * @package Smug\Core\Service\Base\Components\Writer
 */
interface WriterInterface
{
    /**
     * @param array $document
     * @param $pages
     * @param boolean $preview
     * @return string|array
     */
    public function write(array $document, $pages, $preview = false);
}
