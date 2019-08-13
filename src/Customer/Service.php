<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Card;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries create a Customer.
     *
     * @param string $email
     * @param string $description
     * @param Card|null $card
     *
     * @return Customer
     */
    public function create(string $email, string $description, Card $card = null): Customer;

    /**
     * Tries retrieve a Customer
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function retrieve(Token $token): Customer;
}
