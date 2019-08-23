<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Card\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Card\Exception as CardException;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;

class Service implements ServiceInterface
{
    /**
     * Tries create a card using the Stripe SDK.
     *
     * @param Token|null $token
     *
     * @return Card
     *
     * @throws CardException
     */
    public function create(Token $token = null): Card
    {
        if ($token) {
            /** @var SetupIntent $response */
            $response = SetupIntent::retrieve($token->value);
        } else {
            /** @var SetupIntent $response */
            $response = SetupIntent::create([
                'usage' => 'on_session',
                'payment_method_types' => ['card'],
            ]);
        }

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            $token = new StripeToken($response->client_secret);

            throw new CardException($token);
        }

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = PaymentMethod::retrieve($response->payment_method);

        return new Card(
            $paymentMethod->id,
            $paymentMethod->card->last4,
            $paymentMethod->card->brand,
            $paymentMethod->card->funding,
            $paymentMethod->card->exp_month,
            $paymentMethod->card->exp_year
        );
    }

    /**
     * Tries remove a card using the Stripe SDK.
     *
     * @param Card $card
     */
    public function delete(Card $card): void
    {
        $paymentMethod = PaymentMethod::retrieve($card->uid);

        $paymentMethod->detach();
    }

    /**
     * Tries retrieve a card using the Stripe SDK.
     *
     * @param Token $token
     *
     * @return Card
     */
    public function find(Token $token): Card
    {
        $paymentMethod = PaymentMethod::retrieve($token->value);

        return new Card(
            $paymentMethod->id,
            $paymentMethod->card->last4,
            $paymentMethod->card->brand,
            $paymentMethod->card->funding,
            $paymentMethod->card->exp_month,
            $paymentMethod->card->exp_year
        );
    }
}
