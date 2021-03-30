<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

class AmountTooLarge extends \Exception
{
    public $amountCapturable;

    /**
     * @param int $amountCapturable
     * @param \Throwable|null $previous
     */
    public function __construct(int $amountCapturable, \Throwable $previous = null)
    {
        parent::__construct('Failed to create payment due to Stripe API response.', 1, $previous);

        $this->amountCapturable = $amountCapturable;
    }
}
