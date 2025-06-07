<?php

namespace Smug\Core\Helper\Frontend\Factory;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FrontendPositionStyleFactory
 * @package Smug\Core\Service\Frontend\Factory
 */
class FrontendPositionStyleFactory
{
    /** @var string $positionsFile */
    private $positionsFile = 'public/Resources/css/';

    /** @var string $rootDir */
    private $rootDir;

    const VIEWPORTS = [
        'xs' => '0px',
        's' => '768px',
        'm' => '1024px',
        'l' => '1280px'
    ];

    /**
     * FrontendPositionStyleFactory constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->rootDir = $kernel->getProjectDir();
    }

    public function getFrontendPositionsFilePath(string $file = 'positions')
    {
        return $this->rootDir . '/' . $this->positionsFile . $file . '.css';
    }

    /**
     * @param array $entry
     * @return string
     */
    public function createFrontendPositionStyles(array $entry)
    {
        $buffer = '';
	
        foreach ($entry['content'] as $content) {
            if ($content['active'] === true) {
                $buffer .= $this->getPositionStyles(
                    $this->refactorPositions($content['positions']),
                    $content['siteData'],
                    $content['frontId'],
                    $entry['id']
                );
            }
        }

        foreach (self::VIEWPORTS as $key => $viewPortSize) {
            if ($key !== 'xs') {
                $buffer .= "@media screen and (min-width: " . $viewPortSize . ") {\n";
            } else {
                $buffer .= "@media screen and (min-width: 0) {\n";
            }

            $buffer .= ".content--inner.entry--" . $entry['id'] . "{\n";
            $buffer .= "height: " . $content['siteData']['totalRows'][$key] * $content['siteData']['rowHeight'][$key] . "px;\n";

            $buffer .= "}\n}\n";
        }

        return $buffer;
    }

    /**
     * @param array $positions
     * @param array $siteData
     * @param string $frontId
     * @param string $seoName
     * @return string
     */
    private function getPositionStyles(array $positions, array $siteData, $frontId, $seoName)
    {
        $return = '';

        foreach (self::VIEWPORTS as $key => $viewPortSize) {
        	if (!DataHandler::doesKeyExists($key, $positions)) {
                if ($key !== 'xs') {
                    $return .= "@media screen and (min-width: " . $viewPortSize . ") {\n";
                } else {
                    $return .= "@media screen and (min-width: 0) {\n";
                }

                $return .= "#" . $frontId . " {\n";
                $return .= "display: none;\n";
                $return .= "}\n}\n";
	        } else {
                $styles = $positions[$key]['styles'];

                if (!DataHandler::doesKeyExists('margin', $styles)) {
                    $styles['margin'] = [
                        'left' => 0,
                        'right' => 0,
                        'top' => 0,
                        'bottom' => 0
                    ];
                }

                if (!DataHandler::doesKeyExists('padding', $styles)) {
                    $styles['padding'] = [
                        'left' => 0,
                        'right' => 0,
                        'top' => 0,
                        'bottom' => 0
                    ];
                }

                if ($key !== 'xs') {
                    $return .= "@media screen and (min-width: " . $viewPortSize . ") {\n";
                } else {
                    $return .= "@media screen and (min-width: 0) {\n";
                }

                $marginLeft = DataHandler::getIntFromString($styles['margin']['left']);
                $marginRight = DataHandler::getIntFromString($styles['margin']['right']);
                $marginTop = DataHandler::getIntFromString($styles['margin']['top']);
                $marginBottom = DataHandler::getIntFromString($styles['margin']['bottom']);
                $paddingLeft = DataHandler::getIntFromString($styles['padding']['left']);
                $paddingRight = DataHandler::getIntFromString($styles['padding']['right']);
                $paddingTop = DataHandler::getIntFromString($styles['padding']['top']);
                $paddingBottom = DataHandler::getIntFromString($styles['padding']['bottom']);

                if ($positions[$key]['y'] !== 0) {
                    $positions[$key]['y'] = ($positions[$key]['y'] * 100) / $siteData['totalRows'][$key];
                }

                if ($positions[$key]['x'] !== 0) {
                    $positions[$key]['x'] = ($positions[$key]['x'] * 100) / $siteData['totalColumns'];
                }

                $height = $positions[$key]['rows'] * $siteData['rowHeight'][$key] - (($marginBottom + $marginTop));
                $return .= "#" . $frontId . " {\n";

                $return .= "top: calc(" . $positions[$key]['y'] . "% + " . $marginTop . "px);\n";
                $return .= "left: calc(" . $positions[$key]['x'] . "% + " . $marginLeft . "px);\n";
                $return .= "height: " . $height . "px;\n";
                $return .= "width: " . "calc(" . ($positions[$key]['cols'] * 100) / $siteData['totalColumns'] . "% - " . ($marginLeft + $marginRight) . "px);\n";

                if (!DataHandler::doesKeyExists('hide', $positions[$key])) {
                    $positions[$key]['hide'] = false;
                }

                $return .= "margin: " . $marginTop . "px " . $marginRight . "px " . $marginBottom . "px " . $marginLeft . "px;\n";

                if ($positions[$key]['fontColor'] !== '') {
                    $return .= "color: " . $positions[$key]['fontColor'] . ";\n";
                }

                if ($positions[$key]['background'] !== '') {
                    $return .= "background-color: " . $positions[$key]['background'] . ";\n";
                }

                if (DataHandler::getBooleanValue($positions[$key]['hide']) === true) {
                    $return .= "display: none;\n";
                } else {
                    $return .= "display: block;\n";
                }

                $return .= "}\n";

                $return .= "#" . $frontId . " .element--content-inner {\n";
                $return .= "padding: " . $paddingTop . "px " . $paddingRight . "px " . $paddingBottom . "px " . $paddingLeft . "px;\n";

                $return .= "}\n}\n";
            }
        }

        return $return;
    }

    /**
     * @param array $positions
     * @return array
     */
    private function refactorPositions(array $positions)
    {
        $result = [];

        foreach ($positions as $position) {
            $result[$position['viewport']] = $position;
        }

        return $result;
    }
}
