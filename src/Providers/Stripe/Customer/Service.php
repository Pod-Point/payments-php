<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer;
use PodPoint\Payments\Customer\Service as ServiceInterface;
Use PodPoint\Payments\Providers\Stripe\Base;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Customer as StripeCustomer;

class Service extends Base implements ServiceInterface
{
    /**
     * Tries create a customer using the Stripe SDK.
     *
     * @param string $email
     * @param string $description
     *
     * @return Customer
     */
    public function create(string $email, string $description): Customer
    {
        $response = StripeCustomer::create([
            'email' => $email,
            'description' => $description,
        ]);

        return new Customer($response->id, $response->email, $response->description);
    }

    /**
     * Tries retrieve a customer using the Stripe SDK.
     *
     * @param Token $token
     *
     * @return Customer
     */
    public function update(Token $token): Customer
    {
        if ($token->type === StripeToken::CUSTOMER) {
            $response = StripeCustomer::retrieve($token->value);
        } else {
            throw new \Exception("You need to pass a Token with customer type.");
        }

        return new Customer($response->id, $response->email, $response->description);
    }
}
