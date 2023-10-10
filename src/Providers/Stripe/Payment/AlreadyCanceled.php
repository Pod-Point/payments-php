<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

class AlreadyCanceled extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to cancel payment: The stripe PaymentIntent needs to be of status "requires_payment_method", "requires_capture", "requires_confirmation", "requires_action" or "processing" for it to be cancellable.');
    }
}
