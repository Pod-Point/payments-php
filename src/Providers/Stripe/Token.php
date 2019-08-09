<?php

namespace PodPoint\Payments\Providers\Stripe;

class Token extends \PodPoint\Payments\Token
{
    const CUSTOMER = 'customer';
    const PAYMENT_INTENT = 'payment_intent_id';
    const PAYMENT_METHOD = 'payment_method';
    const CHARGE = 'charge';
    const UNDEFINED = 'undefined';
}
