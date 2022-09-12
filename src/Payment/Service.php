<?php

namespace PodPoint\Payments\Payment;

use PodPoint\Payments\Card\Service as CardService;
use PodPoint\Payments\Customer\Service as CustomerService;
use PodPoint\Payments\Exceptions\InvalidToken;
use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\AmountTooLarge;
use PodPoint\Payments\Providers\Stripe\Payment\Exception;
use PodPoint\Payments\Refund\Service as RefundService;
use PodPoint\Payments\Token;
use Stripe\Error\InvalidRequest;

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
     * @param string|null $customerUid
     * @param array $params
     * @param bool $isOffline
     *
     * @return Payment
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        string $description = null,
        array $metadata = [],
        string $customerUid = null,
        array $params = [],
        bool $isOffline = false
    ): Payment;

    /**
     * Tries to reserve funds.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param array $params
     *
     * @return Payment
     *
     * @throws Exception|\Stripe\Error\Api
     */
    public function reserve(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        array $params = []
    ): Payment;

    /**
     * Tries to capture reserved funds.
     *
     * @param Token $token
     * @param int $amount
     *
     * @return Payment
     *
     * @throws AmountTooLarge
     * @throws InvalidRequest
     * @throws InvalidToken
     */
    public function capture(Token $token, int $amount): Payment;

    /**
     * Tries to cancel a payment.
     *
     * @param Token $token
     *
     * @return Payment
     */
    public function cancel(Token $token): Payment;

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
