<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Entity\Customer;
use PodPoint\Payments\Entity\Payment;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;

interface Service
{
    /**
     * Tries make a payment.
     *
     * @param string $token
     * @param int $amount
     * @param string|null $customerId
     * @param string|null $description
     * @param array $metadata
     * @param string $currency
     *
     * @return Payment
     */
    public function create(
        string $token,
        int $amount,
        ?string $customerId,
        ?string $description,
        array $metadata,
        string $currency = 'GBP'
    ): Payment;

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

    /**
     * Creates Stripe customer.
     *
     * @param string $email
     * @param string $paymentMethod
     * @param string|null $description
     *
     * @return Customer
     */
    public function createCustomer(string $email, string $paymentMethod, ?string $description = null): Customer;
}
