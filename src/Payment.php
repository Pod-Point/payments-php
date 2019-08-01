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
     * @var string
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
     * @param string $amount
     * @param string $currency
     * @param int $timestamp
     */
    public function __construct(string $uid, string $amount, string $currency, int $timestamp)
    {
        $this->uid = $uid;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->timestamp = $timestamp;
    }
}
