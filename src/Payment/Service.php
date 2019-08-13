<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries make a payment.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     *
     * @return Payment
     */
    public function create(Token $token, int $amount, string $currency = 'GBP'): Payment;
}
