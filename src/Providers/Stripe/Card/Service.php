<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

Use PodPoint\Payments\Providers\Stripe\Base;
use PodPoint\Payments\Card;
use PodPoint\Payments\Token;
use PodPoint\Payments\Card\Service;
use PodPoint\Payments\Exception;
use PodPoint\Payments\Providers\Stripe\Exception as StripeException;
use Stripe\SetupIntent;

class CardService extends Base implements Service
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
        try {
            if (is_null($token)) {
                $response = SetupIntent::create([
                    'usage' => 'on_session',
                    'payment_method_types' => ['card'],
                ]);
            } else {
                $response = SetupIntent::retrieve($token->value);
            }
        } catch (\Exception $exception) {
            throw new Exception($exception);
        }

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Card($response->payment_method, $response->created);
    }
}
