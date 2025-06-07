<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

class BarcodeProvider
{
    /** @var integer $scale */
    private $scale = 2;

    /** @var integer $thickness */
    private $thickness = 25;

    /** @var integer $fontSize */
    private $fontSize = 10;

    public function __construct($scale = null, $thickness = null, $fontSize = null)
    {
        if ($scale !== null) {
            $this->scale = $scale;
        }

        if ($thickness !== null) {
            $this->thickness = $thickness;
        }

        if ($fontSize !== null) {
            $this->fontSize = $fontSize;
        }
    }

    public function generateCode11Barcode($text)
    {
        $barcode = new BarcodeGenerator();
        $barcode->setText($text);
        $barcode->setType(BarcodeGenerator::Code11);
        $barcode->setScale($this->scale);
        $barcode->setThickness($this->thickness);
        $barcode->setFontSize($this->fontSize);

        return $barcode;
    }
}
