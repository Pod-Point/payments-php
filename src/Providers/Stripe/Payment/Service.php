<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Customer\Service as CustomerServiceInterface;
use PodPoint\Payments\Refund\Service as RefundServiceInterface;
use PodPoint\Payments\Card\Service as CardServiceInterface;
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
     * Tries to make a payment using the Stripe SDK.
     *
     * @param Token $token
     * @param int $amount
     * @param string $currency
     * @param string|null $description
     * @param array $metadata
     * @param Token|null $customer
     *
     * @return Payment
     *
     * @throws Exception
     */
    public function create(
        Token $token,
        int $amount,
        string $currency = 'GBP',
        string $description = null,
        array $metadata = [],
        Token $customer = null
    ): Payment {
        switch ($token->type) {
            case StripeToken::CUSTOMER:
                $customer = $this->customers()->find($token);

                $cards = $this->customers()->getCards($customer);
                $card = $cards[0];

                $paymentMethodToken = new StripeToken($card->uid);

                if ($paymentMethodToken->type === StripeToken::CARD) {
                    /** @var Charge $response */
                    $response = Charge::create([
                        'customer' => $customer->uid,
                        'amount' => $amount,
                        'currency' => $currency,
                        'description' => $description,
                        'metadata' => $metadata,
                    ]);

                    break;
                }

                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $card->uid,
                    'customer' => $customer->uid,
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
                    'payment_method_types' => ['card'],
                    'description' => $description,
                    'metadata' => $metadata,
                    'use_stripe_sdk' => true,
                ]);

                break;
            default:
                //
        }

        if ($response instanceof PaymentIntent && $response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            $token = new StripeToken($response->client_secret);

            throw new StripeException($token);
        }

        return new Payment($response->id, $response->amount, $response->currency, $response->created);
    }

    /**
     * Returns card service.
     *
     * @return CardServiceInterface
     */
    public function cards(): CardServiceInterface
    {
        return new CardService();
    }

    /**
     * Returns customer service.
     *
     * @return CustomerServiceInterface
     */
    public function customers(): CustomerServiceInterface
    {
        return new CustomerService();
    }

    /**
     * Returns refund service.
     *
     * @return RefundServiceInterface
     */
    public function refunds(): RefundServiceInterface
    {
        return new RefundService();
    }
}
