<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries attach a card to a customer.
     *
     * @param Card $card
     * @param Customer $customer
     */
    public function attach(Card $card, Customer $customer): void;

    /**
     * Tries create a card.
     *
     * @param Token|null $token
     * @param string $usage
     *
     * @return Card
     */
    public function create(Token $token = null, string $usage = 'on_session'): Card;

    /**
     * Tries retrieve cards.
     *
     * @param Token $token
     *
     * @return array[Card]
     */
    public function index(Token $token): array;
}
