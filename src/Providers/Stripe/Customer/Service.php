<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use Stripe\Customer as StripeCustomer;

class Service
{
    public function create(string $paymentMethod, string $email, string $description): Customer
    {
        /** @var StripeCustomer $customer */
        $customer = StripeCustomer::create([
            'email' => $email,
            'payment_method' => $paymentMethod,
            'description' => $description,
        ]);

        return new Customer($customer->id, $customer->email);
    }
}
