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
     * @param Card $card
     */
    public function delete(Card $card): void;

    /**
     * Retrieve a card.
     *
     * @param Token $token
     *
     * @return Card
     */
    public function find(Token $token): Card;
}
