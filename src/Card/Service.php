<?php

namespace PodPoint\Payments\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Retrieves a card.
     *
     * @param string $cardUid
     *
     * @return Card
     */
    public function find(string $cardUid): Card;

    /**
     * Creates a card.
     *
     * @param Token|null $token
     *
     * @return Card
     */
    public function create(Token $token = null): Card;

    /**
     * Deletes a card.
     *
     * @param string $cardUid
     */
    public function delete(string $cardUid): void;
}
