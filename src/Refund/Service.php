<?php

namespace PodPoint\Payments\Refund;

use PodPoint\Payments\Refund;
use PodPoint\Payments\Token;

interface Service
{
    /**
     * Creates Refund.
     *
     * @param Token $token
     * @param int $amount
     * @param string $reason
     * @param array $metadata
     *
     * @return Refund
     */
    public function create(Token $token, int $amount, string $reason, array $metadata): Refund;
}
