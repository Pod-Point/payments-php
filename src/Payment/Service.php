<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception;
use PodPoint\Payments\Customer\Service as CustomerService;
use PodPoint\Payments\Refund\Service as RefundService;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries to make a payment.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param string|null $description
     * @param array $metadata
     *
     * @return Payment
     *
     * @throws Exception
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        string $description = null,
        array $metadata = []
    ): Payment;

    /**
     * @return CustomerService
     */
    public function customers(): CustomerService;

    /**
     * @return RefundService
     */
    public function refunds(): RefundService;
}
