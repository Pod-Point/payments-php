<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

class Canceled extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to create payment: The stripe PaymentIntent is already cancelled.');
    }
}
