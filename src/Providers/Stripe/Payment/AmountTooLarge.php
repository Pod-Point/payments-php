<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

class AmountTooLarge extends \Exception
{
    /**
     * @var int
     */
    public $intendedAmount;

    /**
     * @var int
     */
    public $capturableAmount;

    /**
     * @param int $intendedAmount
     * @param int $capturableAmount
     */
    public function __construct(int $intendedAmount, int $capturableAmount)
    {
        parent::__construct('Failed to create payment due to Stripe API response.');

        $this->capturableAmount = $capturableAmount;
        $this->intendedAmount = $intendedAmount;
    }
}
