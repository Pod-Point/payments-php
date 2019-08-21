<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Attach a card to a customer.
     *
     * @param Token $cardToken
     * @param Token $customer
     *
     * @return Card
     */
    public function attach(Token $cardToken, Token $customerToken): Card;

    /**
     * Remove a card.
     *
     * @param Token $cardToken
     * @param Token|null $customerToken
     */
    public function delete(Token $token, Token $customerToken = null): void;

    /**
     * Create a card.
     *
     * @param Token $token
     * @param array $params
     *
     * @return Card
     */
    public function create(Token $token, array $params = []): Card;

    /**
     * Retrieve cards.
     *
     * @param Token $token
     *
     * @return Card[]
     */
    public function get(Token $token): array;
}
