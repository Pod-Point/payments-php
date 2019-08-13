<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Card\Service as ServiceInterface;
use PodPoint\Payments\Exception;
Use PodPoint\Payments\Providers\Stripe\Base;
use PodPoint\Payments\Providers\Stripe\Card\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\SetupIntent;

class Service extends Base implements ServiceInterface
{
    /**
     * Tries create a card using the Stripe SDK.
     *
     * @param Token|null $token
     *
     * @return Card
     *
     * @throws Exception
     * @throws StripeException
     */
    public function create(Token $token = null): Card
    {
        if (is_null($token)) {
            $response = SetupIntent::create([
                'usage' => 'on_session',
                'payment_method_types' => ['card'],
            ]);
        } else if ($token->type === StripeToken::SETUP_INTENT) {
            $response = SetupIntent::retrieve($token->value);
        } else {
            throw new \Exception("You either need to pass a null Token or a setup intent one.");
        }

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Card($response->payment_method, $response->created);
    }
}
