<?php

namespace PodPoint\Payments;

use PodPoint\Payments\Providers\Stripe\Exception as StripeException;

interface CardService
{
    /**
     * Tries create a new card.
     *
     * @return Card
     *
     * @throws Exception
     * @throws StripeException
     */
    public function create(): Card;
}
