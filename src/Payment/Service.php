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
     * @param array $metadata
     *
     * @return Payment
     */
    public function create(Token $token, int $amount = 0, string $currency = 'GBP', array $metadata = []): Payment;
}
