<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Create a card.
     *
     * @param Token|null $token
     *
     * @return Card
     */
    public function create(Token $token = null): Card;

    /**
     * Remove a card.
     *
     * @param Token $token
     */
    public function delete(Token $token): void;
}
