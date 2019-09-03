<?php

namespace PodPoint\Payments;

class Card
{
    /**
     * The uid or unique identifier of the card.
     *
     * @var string
     */
    public $uid;

    /**
     * Last 4 digits of the card.
     *
     * @var string
     */
    public $last4;

    /**
     * Brand of the card.
     *
     * @var string
     */
    public $brand;

    /**
     * Funding of the card.
     *
     * @var string
     */
    public $funding;

    /**
     * Expiration month of the card.
     *
     * @var int
     */
    public $expirationMonth;

    /**
     * Expiration year of the card.
     *
     * @var int
     */
    public $expirationYear;

    /**
     * @param string $uid
     * @param string|null $last4
     * @param string|null $brand
     * @param string|null $funding
     * @param int|null $expirationMonth
     * @param int|null $expirationYear
     */
    public function __construct(
        string $uid,
        string $last4 = null,
        string $brand = null,
        string $funding = null,
        int $expirationMonth = null,
        int $expirationYear = null
    ) {
        $this->uid = $uid;
        $this->last4 = $last4;
        $this->brand = $brand;
        $this->funding = $funding;
        $this->expirationMonth = $expirationMonth;
        $this->expirationYear = $expirationYear;
    }
}
