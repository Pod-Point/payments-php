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
     * @param string $usage
     *
     * @return Card
     */
    public function create(Token $token = null, string $usage = 'on_session'): Card;
}
