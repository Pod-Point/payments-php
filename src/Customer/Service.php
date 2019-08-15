<?php

namespace PodPoint\Payments\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Creates new Customer.
     *
     * @param Token $token
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(Token $token, string $email, string $description): Customer;
}
