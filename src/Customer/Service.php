<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries create a Customer.
     *
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(string $email, string $description): Customer;

    /**
     * Tries retrieve a Customer
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function update(Token $token): Customer;
}
