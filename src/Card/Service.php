<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries make a payment.
     *
     * @param Token|null $token
     *
     * @return Card
     */
    public function create(Token $token = null): Payment;
}
