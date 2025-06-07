<?php

namespace Smug\Core\Service\Base\Components\Preparer;

use Smug\Core\Service\Base\Components\Handler\DataHandler;

class PricePreparer
{
    public function preparePrice($price)
    {
        if (DataHandler::isCharInString('.', $price)) {
            $price = DataHandler::getFloatFromString($price);
            return DataHandler::getNumberWithDecimals($price, 2);
        } elseif (DataHandler::isCharInString(',', $price)) {
            $replacedValue = DataHandler::getReplaceString(',', '.', $price);
            $price = DataHandler::getFloatFromString($replacedValue);
            return DataHandler::getNumberWithDecimals($price, 2);
        } else {
            return DataHandler::getNumberWithDecimals($price, 2);
        }
    }
}
