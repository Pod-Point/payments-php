<?php

namespace PodPoint\Payments;

class Card
{
    /**
     * The uid or unique identifier for a payment method.
     *
     * @var string
     */
    public $uid;

    /**
     * The timestamp the card was created.
     *
     * @var int
     */
    public $timestamp;

    /**
     * @param string $uid
     * @param int $timestamp
     */
    public function __construct(string $uid, int $timestamp)
    {
        $this->uid = $uid;
        $this->timestamp = $timestamp;
    }
}