<?php

namespace Smug\Core\Service\Base\Components\Handler;

use Smug\Core\Service\Base\Components\Provider\DataProvider\AmountProvider;

/**
 * Class PriceHandler
 * @package Smug\Core\Service\Base\Components\Handler
 */
class PriceHandler
{
    /** @var AmountProvider $amountProvider */
    protected $amountProvider;

    /** PriceHandler constructor. */
    public function __construct()
    {
        $this->amountProvider = new AmountProvider();
    }

    /**
     * @param array $tax
     * @param $price
     * @return float
     */
    public function getGrossPriceFromNetPrice(array $tax, $price)
    {
        $tax = 1 + ($tax['value'] / 100);

        return $this->amountProvider->calculateAmount($price * $tax);
    }

    /**
     * @param $total
     * @param $value
     * @return float
     */
    public function getPricePercentage($total, $value)
    {
        return ($value / $total) * 100;
    }
}
