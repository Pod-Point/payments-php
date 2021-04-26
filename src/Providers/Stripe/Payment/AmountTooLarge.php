<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

class AmountTooLarge extends \Exception
{
    /**
     * @var int
     */
    public $capturableAmount;

    /**
     * @param int $capturableAmount
     */
    public function __construct(int $capturableAmount)
    {
        parent::__construct('Failed to create payment due to Stripe API response.');

        $this->capturableAmount = $capturableAmount;
    }
}
