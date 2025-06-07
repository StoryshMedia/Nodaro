<?php

namespace Smug\Core\Service\Base\Components\Provider\DataProvider;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

/**
 * Class AmountProvider
 * @package Smug\Core\Service\Base\Components\Provider\DataProvider
 */
class AmountProvider
{
    const ROUND_PRECISION = 2;
    
    /**
     * This function provides a proper amount value, which is rounded correctly and have a proper format.
     * @param $amount
     * @return float
     */
    public function calculateAmount($amount)
    {
        // replace eventual comma with dot
        if (DataHandler::isCharInString(',', $amount)) {
            $amount = DataHandler::getReplaceString(',', '.', $amount);
        }

        // ensure that the amount is a float value
        $amount = DataHandler::getFloatFromString($amount);

        return $this->roundAmount($amount);
    }

    /**
     * @param float $value
     * @return float
     */
    public static function roundAmount($value)
    {
        $pow = pow(10, self::ROUND_PRECISION);
        $roundedNumber = (ceil($pow * $value) + ceil($pow * $value - ceil($pow * $value))) / $pow;

        return sprintf('%0.2f', $roundedNumber);
    }
}
