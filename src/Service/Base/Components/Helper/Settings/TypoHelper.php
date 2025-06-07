<?php

namespace Smug\Core\Service\Base\Components\Helper\Settings;

/**
 * Class TypoHelper
 * @package Smug\Core\Service\Base\Components\Helper\Settings
 */
class TypoHelper
{
    /**
     * @param array $typoSettings
     * @return string
     */
    public function getTypoStyles(array $typoSettings)
    {
        return $this->generateStyle($typoSettings);
    }

    /**
     * @param array $styles
     * @return string
     */
    private function generateStyle(array $styles)
    {
        $returnString = '';

        foreach ($styles as $style) {
            if ($style['style'] === 'font-size') {
                $style['value'] = $style['value'] . 'px';
            }
            $returnString .= "@" . $style['model'] . ": " . $style['value'] . ";\n";
        }

        return $returnString;
    }
}
