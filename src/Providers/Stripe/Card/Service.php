<?php

namespace PodPoint\Payments\Providers\Stripe\Card;

use PodPoint\Payments\Card;
use PodPoint\Payments\Customer;
use PodPoint\Payments\Card\Service as ServiceInterface;
use PodPoint\Payments\Exception;
Use PodPoint\Payments\Providers\Stripe\Base;
use PodPoint\Payments\Providers\Stripe\Card\Exception as StripeException;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\SetupIntent;
use Stripe\PaymentMethod;

class Service extends Base implements ServiceInterface
{
    /**
     * Tries attach a card to a customer using the Stripe SDK.
     *
     * @param Card $card
     * @param Customer $customer
     */
    public function attach(Card $card, Customer $customer): void
    {
        $paymentMethod = PaymentMethod::retrieve($card->uid);

        $paymentMethod->attach(['customer' => $customer->uid]);
    }

    /**
     * Tries create a card using the Stripe SDK.
     *
     * @param Token|null $token
     * @param string usage
     *
     * @return Card
     *
     * @throws Exception
     * @throws StripeException
     */
    public function create(Token $token = null, string $usage = 'on_session'): Card
    {
        if (is_null($token)) {
            $response = SetupIntent::create([
                'usage' => $usage,
                'payment_method_types' => ['card'],
            ]);
        } else if ($token->type === StripeToken::SETUP_INTENT) {
            $response = SetupIntent::retrieve($token->value);
        } else {
            throw new \Exception('You either need to pass a null Token or a setup intent one.');
        }

        if ($response->status !== SetupIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Card($response->payment_method, $response->created);
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
                        $cards[] = new Card($paymentMethod->id, $paymentMethod->created, $paymentMethod->card->__toArray());
                    }
                }

                break;
            default:
                throw new \Exception("You need to pass a Token with customer type.");

                break;

        }

        return $cards;
    }
}
