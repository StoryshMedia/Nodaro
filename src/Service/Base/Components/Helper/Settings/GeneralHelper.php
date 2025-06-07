<?php

namespace Smug\Core\Service\Base\Components\Helper\Settings;

/**
 * Class GeneralHelper
 * @package Smug\Core\Service\Base\Components\Helper\Settings
 */
class GeneralHelper
{
    /**
     * @param array $generalSettings
     * @return string
     */
    public function getGeneralStyles(array $generalSettings): string
    {
        return $this->generateStyle($generalSettings);
    }

    /**
     * @param array $styles
     * @return string
     */
    private function generateStyle(array $styles): string
    {
        $returnString = '';

        foreach ($styles as $style) {
            $returnString .= "@" . $style['model'] . ": " . $style['value'] . ";\n";
        }

        return $returnString;
    }
}
