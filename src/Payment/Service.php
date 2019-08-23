<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Customer\Service as CustomerService;
use PodPoint\Payments\Refund\Service as RefundService;
use PodPoint\Payments\Card\Service as CardService;
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
     * @param string|null $customerUId
     *
     * @return Payment
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        string $description = null,
        array $metadata = [],
        string $customerUId = null
    ): Payment;

    /**
     * Returns card service.
     *
     * @return CardService
     */
    public function cards(): CardService;

    /**
     * Returns customer service.
     *
     * @return CustomerService
     */
    public function customers(): CustomerService;

    /**
     * Returns refund service.
     *
     * @return RefundService
     */
    public function refunds(): RefundService;
}
