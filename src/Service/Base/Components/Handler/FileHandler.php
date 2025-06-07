<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Smug\Core\Service\Base\Components\Helper\Settings\ColorHelper;
use Smug\Core\Service\Base\Components\Helper\Settings\GeneralHelper;
use Smug\Core\Service\Base\Components\Helper\Settings\LogoHelper;
use Smug\Core\Service\Base\Components\Helper\Settings\TypoHelper;

class FileHandler
{
    /** @var string $settingsFile */
    private string $settingsFile = 'views/_resources/public/less/_variables/settings.less';

    /** @var ColorHelper $colorHelper */
    private ColorHelper $colorHelper;

    /** @var TypoHelper $typoHelper */
    private TypoHelper $typoHelper;

    /** @var GeneralHelper $generalHelper */
    private GeneralHelper $generalHelper;

    /** @var LogoHelper $logoHelper */
    private LogoHelper $logoHelper;

    /** @var string $projectDir */
    protected string $projectDir;

    /**
     * FileHandler constructor.
     * @param string $projectDir
     */
    public function __construct(
        string $projectDir
    ) {
        $this->colorHelper = new ColorHelper();
        $this->typoHelper = new TypoHelper();
        $this->generalHelper = new GeneralHelper();
        $this->logoHelper = new LogoHelper();
        $this->projectDir = $projectDir;
    }

    public function writeSettingsFile(array $settings): bool
    {
        $buffer = '';
        $path = $this->projectDir . '/templates/_frontend/' . $settings['template'] . '/' . $this->settingsFile;

        foreach ($settings['values'] as $key => $category) {
            switch (DataHandler::getLowerString($key)) {
                case 'colors':
                    $buffer .= $this->colorHelper->getColorStyles($category);
                    break;
                case 'typo':
                    $buffer .= $this->typoHelper->getTypoStyles($category);
                    break;
                case 'general':
                    $buffer .= $this->generalHelper->getGeneralStyles($category);
                    break;
                case 'logos':
                    $buffer .= $this->logoHelper->getLogoStyles($category);
                    break;
                default:
                    break;
            }
        }

        DataHandler::writeFile($path, $buffer);

        return true;
    }
}
