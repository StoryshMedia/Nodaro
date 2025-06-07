<?php

namespace Smug\Core\Helper\Frontend\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Class PositionFactory
 * @package Smug\Core\Service\Frontend\Factory
 */
class PositionFactory
{
    private $viewPorts;

    /**
     * PositionFactory constructor.
     */
    public function __construct()
    {
        $this->viewPorts = ['l', 'm', 's', 'xs'];
    }

    /**
     * @param array $entry
     * @param string $vp
     * @return array
     */
    public function getExistingContentMatrix(array $entry, $vp = '')
    {
        $matrix = [];

        foreach ($this->viewPorts as $viewPort) {
            if ($vp !== '' && $viewPort !== $vp) {
                continue;
            }

            for ($i = 0; $i < $entry['rows'][$viewPort]; $i++) {
                $matrix[$viewPort][$i] = [];

                for ($j = 0; $j < $entry['columns']; $j++) {
                    $matrix[$viewPort][$i][$j] = [
                        'reserved' => false
                    ];
                }
            }

            foreach ($entry['content']['elements'][$viewPort] as $content) {
                if (DataHandler::doesKeyExists('hide', $content) && !$content['hide']) {
                    for ($i = $content['y']; $i < $content['y'] + $content['rows']; $i++) {
                        for ($j = $content['x']; $j <= $content['x'] + $content['cols']; $j++) {
                            $matrix[$viewPort][$i][$j]['reserved'] = true;
                        }
                    }
                }
            }
        }

        return $matrix;
    }

    /**
     * @param array $matrix
     * @param array $content
     * @param string $vp
     * @return array
     */
    public function getUnreservedPosition(array $matrix, array $content, $vp = '')
    {
        $positions = [];

        foreach ($this->viewPorts as $viewPort) {
            $positions[$viewPort] = [
                'x' => null,
                'y' => null
            ];
        };

        foreach ($matrix as $key => $viewPort) {
            if ($vp !== '' && $vp !== $key) {
                continue;
            }

            $left = null;
            $top = null;
            $addRows = null;

            if (DataHandler::isInteger($content['rows'])) {
                $contentRows[$key] = $content['rows'];
                $content['rows'] = $contentRows;
            }

            if (!DataHandler::doesKeyExists($key, $content['rows'])) {
                $content['rows'][$key] = 1;
            }

            foreach ($viewPort as $topOffset => $row) {
                foreach ($row as $leftOffset => $column) {
                    if ($column['reserved'] === false) {
                        $unreserved = true;

                        // check all fields within the widget area if they are reserved
                        for ($i = $topOffset; $i < $topOffset + $content['rows'][$key]; $i++) {
                            for ($j = $leftOffset; $j < $leftOffset + $content['cols']; $j++) {
                                if (!DataHandler::doesKeyExists($i, $viewPort)) {
                                    $unreserved = false;
                                    continue;
                                }

                                if (!DataHandler::doesKeyExists($j, $viewPort[$i])) {
                                    $unreserved = false;
                                    continue;
                                }

                                if ($viewPort[$i][$j]['reserved'] === true) {
                                    $unreserved = false;
                                }
                            }
                        }

                        if ($unreserved === true) {
                            $left = $leftOffset;
                            $top = $topOffset;

                            break;
                        }
                    }
                }

                if ($left !== null && $top !== null) {
                    break;
                }
            }

            if ($left === null && $top === null) {
                $top = DataHandler::getArrayLength($viewPort) + 1;
                $left = 0;
                $addRows = $content['rows'][$key];
            }

            $positions[$key] = [
                'addRows' => $addRows,
                'x' => $left,
                'y' => $top
            ];
        }

        return $positions;
    }
}
