<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries create a Customer.
     *
     * @param Token $token
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(Token $token, string $email, string $description): Customer;

    /**
     * Tries retrieve a Customer
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function retrieve(Token $token): Customer;
}
