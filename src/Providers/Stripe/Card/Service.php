<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Card\Service as ServiceInterface;
use PodPoint\Payments\Exception;
use PodPoint\Payments\Providers\Stripe\Card\Exception as CardException;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;
use Stripe\Customer as StripeCustomer;

class Service implements ServiceInterface
{
    /**
     * Tries attach a card to a customer using the Stripe SDK.
     *
     * @param Token $cardToken
     * @param Token $customerToken
     *
     * @return Card
     */
    public function attach(Token $cardToken, Token $customerToken): Card
    {
        if ($cardToken->type !== StripeToken::PAYMENT_METHOD && $customerToken->type !== StripeToken::CUSTOMER) {
            throw new \Exception('You need to provide a valid card Token and customer Token');
        }

        $paymentMethod = PaymentMethod::retrieve($cardToken->value);

        $response = $paymentMethod->attach(['customer' => $customerToken->value]);

        return new Card(
            $response->id,
            $response->card->last4,
            $response->card->brand,
            $response->card->funding,
            $response->card->exp_month,
            $response->card->exp_year
        );
    }

    /**
     * Tries remove a card using the Stripe SDK.
     *
     * @param Token $cardToken
     * @param Token|null $customerToken
     */
    public function remove(Token $cardToken, $customerToken = null): void
    {
        if (!in_array($cardToken->type, [
            StripeToken::PAYMENT_METHOD,
            StripeToken::CARD
        ])) {
            throw new \Exception('You need to provide a valid card/payment method Token.');
        }

        switch ($cardToken->type) {
            case StripeToken::PAYMENT_METHOD:
                $paymentMethod = PaymentMethod::retrieve($cardToken->value);

                $paymentMethod->detach();

                break;
            case StripeToken::CARD:
                if ($customerToken->type !== StripeToken::CUSTOMER) {
                    throw new \Exception('You need to provide a valid customer Token.');
                }

                StripeCustomer::deleteSource($customerToken->value, $cardToken->value);

                break;
        }

    }

    /**
     * Tries create a card using the Stripe SDK.
     *
     * @param Token $token
     * @param array $params
     *
     * @return Card
     *
     * @throws Exception
     * @throws CardException
     */
    public function create(Token $token, array $params = []): Card
    {
        switch ($token->type) {
            case StripeToken::UNDEFINED:
                $params = array_merge(
                    [
                        'usage' => 'on_session',
                        'payment_method_types' => ['card'],
                    ],
                    $params
                );

                $response = SetupIntent::create($params);

                break;
            case StripeToken::SETUP_INTENT:
                $response = SetupIntent::retrieve($token->value);

                break;
            default:
                throw new \Exception('You either need to pass a null Token or a setup intent one.');
        }

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            $token = new Token($response->client_secret);

            throw new CardException($token);
        }

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
     * Tries get cards using the Stripe SDK.
     *
     * @param Token $token
     *
     * @return array[Card]
     */
    public function index(Token $token): array
    {
        $cards = [];

        switch ($token->type)
        {
            case StripeToken::CUSTOMER:
                $paymentMethods = PaymentMethod::all([
                    "customer" => $token->value,
                    "type" => "card",
                ]);

                if (isset($paymentMethods->data) && $paymentMethods->data) {
                    foreach ($paymentMethods->data as $paymentMethod) {
                        $cards[] = new Card(
                            $paymentMethod->id,
                            $paymentMethod->card->last4,
                            $paymentMethod->card->brand,
                            $paymentMethod->card->funding,
                            $paymentMethod->card->exp_month,
                            $paymentMethod->card->exp_year
                        );
                    }
                }

                $customer = StripeCustomer::retrieve($token->value);

                if (isset($customer->cards->data) && $customer->cards->data) {
                    foreach ($customer->cards->data as $card) {
                        $cards[] = new Card(
                            $card->id,
                            $card->last4,
                            $card->brand,
                            $card->funding,
                            $card->exp_month,
                            $card->exp_year
                        );
                    }
                }

                break;
            default:
                throw new \Exception("You need to pass a customer Token.");

                break;
        }

        return $cards;
    }
}
