<?php

namespace Smug\Core\Service\Base\Components\Helper\Settings;

/**
 * Class ColorHelper
 * @package Smug\Core\Service\Base\Components\Helper\Settings
 */
class ColorHelper
{
    /**
     * @param array $colorSettings
     * @return string
     */
    public function getColorStyles(array $colorSettings)
    {
        return $this->generateStyle($colorSettings);
    }

    /**
     * @param array $styles
     * @return string
     */
    private function generateStyle(array $styles)
    {
        $returnString = '';

        foreach ($styles as $style) {
            $returnString .= "@" . $style['model'] . ": " . $style['value'] . ";\n";
        }

        return $returnString;
    }
}
