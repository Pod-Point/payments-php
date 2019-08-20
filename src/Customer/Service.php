<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Customer;
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
     * Retrieve a Customer
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function retrieve(Token $token): Customer;
}
