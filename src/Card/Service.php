<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Tries create a card.
     *
     * @param Token|null $token
     *
     * @return Card
     */
    public function create(Token $token = null): Card;
}
