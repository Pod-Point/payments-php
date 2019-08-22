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
     * Retrieve a Customer.
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function find(Token $token): Customer;

    /**
     * Add a card to a customer.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return Card
     */
    public function addCard(Customer $customer, Card $card): Card;

    /**
     * Remove a card from a customer.
     *
     * @param Customer $customer
     * @param Card $card
     *
     * @return void
     */
    public function deleteCard(Customer $customer, Card $card): void;

    /**
     * Get customer's cards.
     *
     * @param Customer $customer
     *
     * @return Card[]
     */
    public function getCards(Customer $customer): array;
}
