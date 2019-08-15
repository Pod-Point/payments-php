<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer\Service as ServiceInterface;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;
use Stripe\Customer as StripeCustomer;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;

class Service implements ServiceInterface
{
    /**
     * Tries create a customer using the Stripe SDK can be initiated with attached method|card based on incoming token type.
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

        $response = StripeCustomer::create($params);

        return new Customer($response->id, $response->email, $response->description);
    }

    /**
     * Tries retrieve a customer using the Stripe SDK.
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function retrieve(Token $token): Customer
    {
        if ($token->type === StripeToken::CUSTOMER) {
            $response = StripeCustomer::retrieve($token->value);
        } else {
            throw new \Exception("You need to pass a Token with customer type.");
        }

        return new Customer($response->id, $response->email, $response->description);
    }
}
