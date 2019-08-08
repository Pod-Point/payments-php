<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Token;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;

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
    public function create(Token $token, int $amount = null, string $currency = 'GBP'): Payment;

    /**
     * @param int $amount
     * @param string $description
     * @param array $metadata
     * @param string $currency
     * @return string
     */
    public function initialize(
        int $amount,
        string $description,
        array $metadata,
        string $currency = 'GBP'
    ): string;

    /**
     * @return CustomerService
     */
    public function customer(): CustomerService;
}
