<?php

namespace Smug\Core\Service\Base\Components\Preparer;

use Smug\Core\Service\Base\Components\Handler\DataHandler;
use Smug\Core\Service\Base\Components\Handler\TimeHandler;
use Smug\Core\Service\Base\Components\Provider\DataProvider\NumberProvider;
use Smug\Core\Service\Base\Components\Provider\DataProvider\TimeProvider;

class DocumentNumberPreparer
{
    public static function generateDocumentNumber(array $format, $lastOrderNumber, $prefix)
    {
        $lastOrderNumber = self::removePrefixes($lastOrderNumber, $prefix);

        switch ($format['value']) {
            case 'date':
                $billingNumber = TimeHandler::getDate(TimeProvider::DOCUMENT_NUMBER_TIME_FORMAT) .
                    NumberProvider::getRandomNumber();
                break;
            case 'running':
                $billingNumber = DataHandler::getIntFromString($lastOrderNumber) + 1;
                break;
            case 'runningWithAdd':
                $billingNumber = (DataHandler::getIntFromString($lastOrderNumber) + 1) .
                    NumberProvider::getRandomNumber();
                break;
            default:
                $billingNumber = TimeHandler::getDate(TimeProvider::DOCUMENT_NUMBER_TIME_FORMAT) .
                    NumberProvider::getRandomNumber();
                break;
        }

        return $billingNumber;
    }

    private static function removePrefixes($lastOrderNumber, $prefix)
    {
        return DataHandler::getReplaceString($prefix, '', $lastOrderNumber);
    }
}
