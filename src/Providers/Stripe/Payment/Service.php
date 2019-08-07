<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Payment\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Card;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class Service implements ServiceInterface
{
    /**
     * @param string $key
     */
    public function __construct(string $key)
    {
        Stripe::setApiKey($key);
    }

    /**
     * Tries make a payment using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     *
     * @return Payment
     *
     * @throws StripeException
     */
    public function create(Token $token, int $amount, string $currency = 'GBP'): Payment
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                $response->confirm();

                break;
            case StripeToken::PAYMENT_METHOD:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                ]);

                break;
            default:
            case StripeToken::CUSTOMER:
                /** @var Card $response */
                $response = Charge::create([
                    'customer' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                ]);

                break;
        }

        if ($response instanceof PaymentIntent && $response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
    }
}
