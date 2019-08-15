<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Providers\Stripe\Card\Service as CardService;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Payment\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
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
     * Creates|confirms Payment.
     * Includes backwards compatibilty in case payment method constains card token instead of payment method token.
     *
     * @param Token $token
     * @param int $amount
     * @param string|null $description
     * @param array $metadata
     * @param string $currency
     * @param Token $customer
     *
     * @return Payment
     *
     * @throws Exception
     */
    public function create(
        Token $token,
        int $amount,
        string $description = null,
        array $metadata = [],
        string $currency = 'GBP',
        $customer = null
    ): Payment {
        switch ($token->type) {
            case StripeToken::CUSTOMER:
                $cards = $this->cards()->index($token);
                $card = $cards[0];

                if ($token->isCard($card->uid)) {
                    /** @var Charge $response */
                    $response = Charge::create([
                        'customer' => $token->value,
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                        'metadata' => $metadata,
                    ]);

                    break;
                }

                $response = PaymentIntent::create([
                    'payment_method' => $card->uid,
                    'customer' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    "payment_method_types" => ["card"],
                    'description' => $description,
                    'metadata' => $metadata,
                    'use_stripe_sdk' => true,
                ]);

                break;
            case StripeToken::TOKEN:
                /** @var Charge $response */
                $response = Charge::create([
                    'amount'      => $amount,
                    'currency'    => $currency,
                    'source'      => $token->value,
                    'metadata'    => $metadata
                ]);

                break;
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                $response->confirm();

                break;
            case StripeToken::PAYMENT_METHOD:
                if ($customer && $customer->type !== StripeToken::CUSTOMER) {
                    throw new \Exception('You need to pass a valid customer token to charge a card one.');
                }

                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $token->value,
                    'customer' => $customer->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    "payment_method_types" => ["card"],
                    'description' => $description,
                    'metadata' => $metadata,
                    'use_stripe_sdk' => true,
                ]);

                break;
        }

        if ($response instanceof PaymentIntent && $response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            $token = new Token($response->client_secret);

            throw new StripeException($token);
        }

        return new Payment($response->id, $response->amount, $response->currency, $response->created);
    }

    public function getProviderName(): string
    {
        return 'stripe';
    }

    public function customers(): CustomerService
    {
        return new CustomerService();
    }

    public function refunds(): RefundService
    {
        return new RefundService();
    }

    public function cards(): CardService
    {
        return new CardService();
    }
}
