<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Retrieves a card.
     *
     * @param string $uid
     *
     * @return Card
     */
    public function find(string $uid): Card;

    /**
     * Creates a card.
     *
     * @param Token|null $token
     *
     * @return Card
     */
    public function create(Token $token = null): Card;
}
