<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Entity\Customer;
use PodPoint\Payments\Entity\Payment;
use PodPoint\Payments\Entity\Refund;
use PodPoint\Payments\Exceptions\PaymentException;
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
     *
     * @throws PaymentException
     * @throws StripeException
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
     * @throws PaymentException
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

    /**
     * Creates Refund for provided Payment Intent id.
     *
     * @param string $intentId
     * @param int|null $amount
     * @param string|null $reason
     * @param array|null $metadata
     *
     * @return Refund
     *
     * @throws StripeException
     * @throws PaymentException
     */
    public function refund(
        string $intentId,
        ?int $amount = null,
        ?string $reason = null,
        ?array $metadata = []
    ): Refund;
}
