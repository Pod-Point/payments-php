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
     * @param array $metadata
     * @param Token|null $customer
     * @param string|null $description
     *
     * @return Payment
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        array $metadata = [],
        $customer = null,
        string $description = null
    ): Payment;

    /**
     * @return CardService
     */
    public function cards(): CardService;

    /**
     * @return CustomerService
     */
    public function customers(): CustomerService;

    /**
     * @return RefundService
     */
    public function refunds(): RefundService;

    /**
     * Return the name of the provider.
     *
     * @return string
     */
    public function getProviderName(): string;
}
