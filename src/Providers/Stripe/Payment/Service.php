<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Payment\Service as ServiceInterface;
Use PodPoint\Payments\Providers\Stripe\Base;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Card;
use Stripe\Charge;
use Stripe\PaymentIntent;

class Service extends Base implements ServiceInterface
{
    /**
     * Tries make a payment using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param array $metadata
     *
     * @return Payment
     *
     * @throws StripeException
     */
    public function create(Token $token, int $amount = 0, string $currency = 'GBP', array $metadata = []): Payment
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                $response = $response->confirm();

                break;
            case StripeToken::PAYMENT_METHOD:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'payment_method' => $token->value,
                    "payment_method_types" => ["card"],
                    'metadata' => $metadata,
                ]);

                break;
            default:
            case StripeToken::CUSTOMER:
                /** @var Card $response */
                $response = Charge::create([
                    'customer' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'metadata'    => $metadata
                ]);

                break;
            case StripeToken::TOKEN:
                /** @var Card $response */
                $response = Charge::create([
                    'amount'      => $amount,
                    'currency'    => $currency,
                    'source'      => $token->value,
                    'metadata'    => $metadata
                ]);

                break;
        }

        if ($response instanceof PaymentIntent && $response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
    }
}
