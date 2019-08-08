<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Payment\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Refund;
use PodPoint\Payments\Token;
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
     * @param int $amount
     * @param string $description
     * @param array $metadata
     * @param string $currency
     * @return string
     */
    public function initialize(
        int $amount,
        string $description,
        array $metadata,
        string $currency = 'GBP'
    ): string {
        $intent = PaymentIntent::create([
            'amount' => (int) $amount,
            'currency' => $currency,
            'setup_future_usage' => 'off_session',
            'description' => $description,
            'metadata' => $metadata,
        ]);

        return $intent->client_secret;
    }

    /**
     * Tries make a payment using the Stripe SDK.
     *
     * @param int $amount
     * @param Token $token
     * @param string $currency
     *
     * @return Payment
     *
     * @throws StripeException
     */
    public function create(Token $token, int $amount = null, string $currency = 'GBP'): Payment
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                break;
            case StripeToken::PAYMENT_METHOD:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'customer' => $token->customer,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'off_session' => true,
                    'payment_method_types' => ['card'],
                ]);
                break;
            default:
            case StripeToken::CUSTOMER:
                /** @var Charge $response */
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

    public function refund(Token $token, int $amount): Refund
    {
        switch ($token->type) {
            case StripeToken::PAYMENT_INTENT:
                $intent = PaymentIntent::retrieve($token->value);

                /** @var Charge $charge */
                $charge = $intent->charges->data[0];
                $charge->refund(['amount' => $amount]);
            default:
                // BC for charges
        }
    }

    public function customer(): CustomerService
    {
        return new CustomerService();
    }
}
