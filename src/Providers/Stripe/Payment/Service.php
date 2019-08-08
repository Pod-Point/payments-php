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
     * @param string $token
     * @param string $customerId
     * @param int|null $amount
     * @param string $currency
     *
     * @return Payment
     *
     * @throws Exception
     */
    public function create(string $token, string $customerId, int $amount = null, string $currency = 'GBP'): Payment
    {
        $tokenType = $this->getTokenType($token);

        switch ($tokenType) {
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token);
                break;
            case StripeToken::PAYMENT_METHOD:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::create([
                    'payment_method' => $token,
                    'amount' => $amount,
                    'currency' => $currency,
                    'customer' => $customerId,
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
                    'customer' => $token,
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

    /**
     * @param string $token
     * @param int $amount
     *
     * @return Refund
     */
    public function refund(string $token, int $amount): Refund
    {
        $tokenType = $this->getTokenType($token);

        switch ($tokenType) {
            case StripeToken::PAYMENT_INTENT:
                $refund = PaymentIntent::retrieve($token);

                /** @var Charge $charge */
                $charge = $refund->charges->data[0];
                $charge->refund(['amount' => $amount]);
                break;
            case StripeToken::CUSTOMER:
                $refund = \Stripe\Refund::create([
                    'charge' => $token,
                    'amount' => $amount,
                ]);
                break;
        }

        return new Refund($refund->id);
    }

    /**
     * @param string $token
     *
     * @return string
     */
    private function getTokenType(string $token): string
    {
        switch (trim(substr($token, 0, 2))) {
            case 'pi':
                return StripeToken::PAYMENT_INTENT;
            case 'pm':
                return StripeToken::PAYMENT_METHOD;
            case 'ch':
                return StripeToken::CUSTOMER;
            default:
                return 'unidentified';
        }
    }

    public function customer(): CustomerService
    {
        return new CustomerService();
    }
}
