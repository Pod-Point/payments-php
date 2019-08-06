<?php

namespace PodPoint\Payments;

class Card
{
    /**
     * The uid or unique identifier of the setup intent.
     *
     * @var string
     */
    public $uid;

    /**
     * The request identifier needed when recording a new card.
     *
     * @var string
     */
    public $requestId;

    /**
     * The customer card identifier.
     *
     * @var string
     */
    public $cardToken;

    /**
     * The timestamp the setup intent was created.
     *
     * @var int
     */
    public $timestamp;

    /**
     * @param string $uid
     * @param string $cardToken
     * @param string $requestId
     * @param int $timestamp
     */
    public function __construct(string $uid, string $cardToken, string $requestId, int $timestamp)
    {
        $this->uid = $uid;
        $this->cardToken = $cardToken;
        $this->requestId = $requestId;
        $this->timestamp = $timestamp;
    }
}