<?php

namespace Smug\Core\Service\Base\Components\Helper\Settings;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Class LogoHelper
 * @package Smug\Core\Service\Base\Components\Helper\Settings
 */
class LogoHelper
{
    /**
     * @param array $logoSettings
     * @return string
     */
    public function getLogoStyles(array $logoSettings)
    {
        return $this->generateStyle($logoSettings);
    }

    /**
     * @param array $styles
     * @return string
     */
    private function generateStyle(array $styles)
    {
        $returnString = '';

        foreach ($styles as $style) {
            if ($style['style'] !== '' && $style['value'] !== '') {
                if (DataHandler::isStringInString($style['value'], '/')) {
                    $returnString .= "@" . $style['model'] . ": " . "'" . $style['value'] . "'" . ";\n";
                } else {
                    $returnString .= "@" . $style['model'] . ": " . $style['value'] . ";\n";
                }
            }
        }

        return $returnString;
    }
}
