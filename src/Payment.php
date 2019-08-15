<?php

namespace PodPoint\Payments;

class Payment
{
    /**
     * The uid or unique identifier of the payment.
     *
     * @var string
     */
    public $uid;

    /**
     * The payment amount.
     *
     * @var int
     */
    public $amount;

    /**
     * The payment currency.
     *
     * @var string
     */
    public $currency;

    /**
     * The timestamp the payment was processed.
     *
     * @var int
     */
    public $timestamp;

    /**
     * @param string $uid
     * @param int $amount
     * @param string $currency
     * @param int $timestamp
     */
    public function __construct(string $uid, int $amount, string $currency, int $timestamp)
    {
        $this->uid = $uid;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->timestamp = $timestamp;
    }
}
