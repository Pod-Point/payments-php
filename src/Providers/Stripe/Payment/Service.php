<?php

namespace PodPoint\Payments\Providers\Stripe\Payment;

use PodPoint\Payments\Payment;
use PodPoint\Payments\Customer\Service as CustomerServiceInterface;
use PodPoint\Payments\Refund\Service as RefundServiceInterface;
use PodPoint\Payments\Providers\Stripe\Customer\Service as CustomerService;
use PodPoint\Payments\Providers\Stripe\Refund\Service as RefundService;
use PodPoint\Payments\Providers\Stripe\Payment\Exception as StripeException;
use PodPoint\Payments\Payment\Service as ServiceInterface;
use PodPoint\Payments\Providers\Stripe\Token as StripeToken;
use PodPoint\Payments\Token;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
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
     * @param string $currency
     * @param string|null $description
     * @param array $metadata
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
        array $metadata = []
    ): Payment {
        switch ($token->type) {
            case StripeToken::CUSTOMER:
                $paymentMethods = PaymentMethod::all([
                    'customer' => $token->value,
                    'type' => 'card',
                ]);

                /** @var PaymentMethod $paymentMethod */
                $paymentMethod = $paymentMethods->data[0];

                if ($token->isCard($paymentMethod->id)) {
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
                    'payment_method' => $paymentMethod->id,
                    'customer' => $token->value,
                    'amount' => $amount,
                    'currency' => $currency,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'description' => $description,
                    'metadata' => $metadata,
                ]);

                break;
            case StripeToken::PAYMENT_INTENT:
                /** @var PaymentIntent $response */
                $response = PaymentIntent::retrieve($token->value);
                $response->confirm();

                break;
        }

        if ($response instanceof PaymentIntent && $response->status !== PaymentIntent::STATUS_SUCCEEDED) {
            throw new StripeException($response);
        }

        return new Payment($response->id, $response->currency, $response->amount, $response->created);
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
