<?php

namespace PodPoint\Payments\Providers\Stripe\Customer;

use PodPoint\Payments\Customer\Service as CustomerServiceInterface;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Token;
use Stripe\Customer as StripeCustomer;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;

class Service implements CustomerServiceInterface
{
    /**
     * Creates a customer using the Stripe SDK.
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

        switch ($token->type) {
            case StripeToken::PAYMENT_METHOD:
                $params['payment_method'] = $token->value;

                break;
            case StripeToken::CARD:
                $params['card'] = $token->value;

                break;
            case StripeToken::TOKEN:
                $params['source'] = $token->value;

                break;
            default:
                throw new \Exception('Unexpected token type.');
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
