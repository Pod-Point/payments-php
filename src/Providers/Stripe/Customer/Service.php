<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;
use Stripe\Customer as StripeCustomer;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;

class Service
{
    /**
     * Creates new Customer with attached method|card based on incoming token type.
     *
     * @param Token $token
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(Token $token, string $email, string $description): Customer
    {
        $params = [
            'email' => $email,
            'description' => $description,
        ];

        if ($token->type === StripeToken::PAYMENT_METHOD) {
            $params['payment_method'] = $token->value;
        } else {
            $params['card'] = $token->value;
        }

        /** @var StripeCustomer $customer */
        $customer = StripeCustomer::create($params);

        return new Customer($customer->id, $customer->email);
    }
}
