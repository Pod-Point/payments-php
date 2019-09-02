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
     * @param string $customerUid
     * @param string $cardUid
     *
     * @return Card
     */
    public function addCard(string $customerUid, string $cardUid): Card;

    /**
     * Deletes a customers card.
     *
     * @param string $customerUid
     * @param string $cardUid
     *
     * @return void
     */
    public function deleteCard(string $customerUid, string $cardUid): void;

    /**
     * Retrieves a customers cards.
     *
     * @param string $uid
     *
     * @return Card[]
     */
    public function getCards(string $uid): array;
}
