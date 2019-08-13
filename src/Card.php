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
     * Data related to the card.
     *
     * @var array
     */
    public $data;

    /**
     * @param string $uid
     * @param int $timestamp
     */
    public function __construct(string $uid, int $timestamp, array $data = [])
    {
        $this->uid = $uid;
        $this->timestamp = $timestamp;
        $this->data = $data;
    }
}