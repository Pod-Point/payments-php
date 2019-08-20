<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception;
use PodPoint\Payments\Providers\Stripe\Card\Service as CardService;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;
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
     *
     * @throws Exception
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
     * Return the name of the provider.
     *
     * @return string
     */
    public function getProviderName(): string;

    /**
     * @return CustomerService
     */
    public function customers(): CustomerService;

    /**
     * @return RefundService
     */
    public function refunds(): RefundService;

    /**
     * @return CardService
     */
    public function cards(): CardService;
}
