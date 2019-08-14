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
     * @param Token $cardToken
     * @param Token $customer
     *
     * @return Card
     */
    public function attach(Token $cardToken, Token $customerToken): Card;

    /**
     * Tries remove a card.
     *
     * @param Token $cardToken
     * @param Token|null $customerToken
     */
    public function remove(Token $token, Token $customerToken = null): void;

    /**
     * Tries create a card.
     *
     * @param Token $token
     * @param array $params
     *
     * @return Card
     */
    public function create(Token $token, array $params = []): Card;

    /**
     * Tries retrieve cards.
     *
     * @param Token $token
     *
     * @return array[Card]
     */
    public function index(Token $token): array;
}
