<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception;
use PodPoint\Payments\Refund;
use PodPoint\Payments\Token;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;

interface Service
{
    /**
     * @param string $token
     * @param string $customerId
     * @param int|null $amount
     * @param string $currency
     *
     * @return Payment
     *
     * @throws Exception
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
     * @param string $token
     * @param int $amount
     *
     * @return Refund
     */
    public function refund(string $token, int $amount): Refund;

    /**
     * @return CustomerService
     */
    public function customer(): CustomerService;
}
