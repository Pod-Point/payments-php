<?php

namespace PodPoint\Payments\Providers\Stripe;

use Stripe\Stripe;

abstract class Base
{
    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        Stripe::setApiKey($key);
    }
}