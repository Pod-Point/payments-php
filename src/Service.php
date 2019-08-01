<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Providers\Stripe\Exception as StripeException;

interface Service
{
    /**
     * Tries make a payment.
     *
     * @param string $token
     * @param int $amount
     * @param string $currency
     *
     * @return Payment
     */
    public function create(string $token, int $amount, string $currency = 'GBP'): Payment;

    /**
     * Tries update a payment.
     *
     * @param string $uid
     *
     * @return Payment
     *
     * @throws Exception
     * @throws StripeException
     */
    public function update(string $uid): Payment;
}
