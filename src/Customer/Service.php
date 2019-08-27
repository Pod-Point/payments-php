<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Creates a customer.
     *
     * @param Token $token
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(Token $token, string $email, string $description): Customer;

    /**
     * Retrieves a customer.
     *
     * @param string $uid
     *
     * @return Customer
     */
    public function find(string $uid): Customer;

    /**
     * Associates a card to a customer.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return Card
     */
    public function addCard(Customer $customer, Card $card): Card;

    /**
     * Deletes a customers card.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return void
     */
    public function deleteCard(Customer $customer, Card $card): void;

    /**
     * Retrieves a customers cards.
     *
     * @param Customer $customer
     *
     * @return Card[]
     */
    public function getCards(Customer $customer): array;
}
