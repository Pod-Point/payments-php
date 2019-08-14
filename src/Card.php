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
     * Data related to the card.
     *
     * @var array
     */
    public $data;

    /**
     * @param string $uid
     * @param array $data
     */
    public function __construct(string $uid, array $data = [])
    {
        $this->uid = $uid;
        $this->data = $data;
    }
}